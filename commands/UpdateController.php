<?php

declare(strict_types=1);

namespace app\commands;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Generator;
use Throwable;
use Yii;
use app\helpers\CountToCidr;
use app\helpers\DownloadFormatter;
use app\helpers\Ipv4byccDumper;
use app\helpers\NginxGeoDumper;
use app\helpers\TypeHelper;
use app\models\AllocationBlock;
use app\models\AllocationCidr;
use app\models\DownloadTemplate;
use app\models\Krfilter;
use app\models\KrfilterCidr;
use app\models\MergedCidr;
use app\models\Region;
use app\models\RegionStat;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\FileHelper;
use yii\httpclient\Client as HttpClient;

use function basename;
use function chdir;
use function count;
use function dirname;
use function escapeshellarg;
use function exec;
use function fclose;
use function feof;
use function fgets;
use function file_exists;
use function file_put_contents;
use function filesize;
use function floor;
use function fopen;
use function fwrite;
use function getcwd;
use function implode;
use function ini_set;
use function microtime;
use function preg_match;
use function proc_close;
use function proc_open;
use function rename;
use function sprintf;
use function str_starts_with;
use function strlen;
use function strtolower;
use function substr;
use function trim;
use function unlink;
use function vsprintf;

use const SORT_ASC;

class UpdateController extends Controller
{
    private const TAG_AFRINIC = 'afrinic';
    private const TAG_APNIC = 'apnic';
    private const TAG_ARIN = 'arin';
    private const TAG_LACNIC = 'lacnic';
    private const TAG_RIPENCC = 'ripe-ncc';

    private const URL_AFRINIC = 'http://ftp.afrinic.net/stats/afrinic/delegated-afrinic-extended-latest';
    private const URL_APNIC = 'http://ftp.apnic.net/stats/apnic/delegated-apnic-extended-latest';
    private const URL_ARIN = 'http://ftp.arin.net/pub/stats/arin/delegated-arin-extended-latest';
    // private const URL_IANA    = 'http://ftp.apnic.net/stats/iana/delegated-iana-latest';
    private const URL_LACNIC = 'http://ftp.lacnic.net/pub/stats/lacnic/delegated-lacnic-extended-latest';
    private const URL_RIPENCC = 'http://ftp.ripe.net/pub/stats/ripencc/delegated-ripencc-extended-latest';

    /** @var array<string, int> */
    private array $checkedCountries = [];

    /** @return void */
    public function init()
    {
        parent::init();
        ini_set('memory_limit', '1G');
    }

    public function actionIndex(bool $skipUpdate = false): int
    {
        /** @var int $status */
        $status = ExitCode::OK;

        /** @var ?float $updateStartAt */
        $updateStartAt = null;

        /** @var ?float $updateFinishAt */
        $updateFinishAt = null;

        if (!$skipUpdate) {
            try {
                $updateStartAt = microtime(true);

                Yii::$app->db->transaction(function (): void {
                    Yii::info('Deleting allocation_cidr', __METHOD__);
                    AllocationCidr::deleteAll('1 = 1');

                    Yii::info('Deleting allocation_block', __METHOD__);
                    AllocationBlock::deleteAll('1 = 1');

                    /** @var array<string, callable> $actions */
                    $actions = [
                        'AfriNIC' => fn (): int => $this->actionAfrinic(),
                        'APNIC' => fn (): int => $this->actionApnic(),
                        'ARIN' => fn (): int => $this->actionArin(),
                        'LACNIC' => fn (): int => $this->actionLacnic(),
                        'RIPE NCC' => fn (): int => $this->actionRipeNcc(),
                    ];
                    foreach ($actions as $label => $action) {
                        Yii::info("Updating {$label}", __METHOD__);
                        $status = $action();
                        if ($status !== ExitCode::OK) {
                            throw new Exception();
                        }
                    }
                });
            } catch (Throwable $e) {
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->actionMerged();
            $this->actionStat();
        }
        $this->actionKrfilter();
        $this->actionIpv4bycc();
        $this->actionNginxGeo();
        $updateFinishAt = microtime(true);

        $this->actionPreformattedGitRepo();

        if ($status !== ExitCode::OK) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (
            !$skipUpdate &&
            $updateStartAt !== null &&
            $updateFinishAt !== null
        ) {
            $this->saveTimeRecord($updateStartAt, $updateFinishAt);
        }

        return ExitCode::OK;
    }

    public function actionAfrinic(): int
    {
        return $this->update(self::TAG_AFRINIC, self::URL_AFRINIC);
    }

    public function actionApnic(): int
    {
        return $this->update(self::TAG_APNIC, self::URL_APNIC);
    }

    public function actionArin(): int
    {
        return $this->update(self::TAG_ARIN, self::URL_ARIN);
    }

    public function actionLacnic(): int
    {
        return $this->update(self::TAG_LACNIC, self::URL_LACNIC);
    }

    public function actionRipeNcc(): int
    {
        return $this->update(self::TAG_RIPENCC, self::URL_RIPENCC);
    }

    private function update(string $tag, string $url): int
    {
        if (!$list = $this->downloadAndParse($tag, $url)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $debugCategory = __METHOD__;
        Yii::$app->db->transaction(function (Connection $db) use ($debugCategory, $list, $tag): void {
            Yii::info("Deleting allocation_cidr ({$tag})", $debugCategory);
            $sql = implode(' ', [
                'DELETE FROM {{%allocation_cidr}}',
                'USING {{%allocation_block}}',
                'WHERE {{%allocation_cidr}}.[[block_id]] = {{%allocation_block}}.[[id]]',
                'AND {{%allocation_block}}.[[registry_id]] = ' . $db->quoteValue($list[0]['registry']),
            ]);
            $db->createCommand($sql)->execute();

            Yii::info("Deleting allocaiton_block ({$tag})", $debugCategory);
            AllocationBlock::deleteAll([
                'registry_id' => $list[0]['registry'],
            ]);

            Yii::info("Updating ({$tag})", $debugCategory);
            foreach ($list as $info) {
                $this->updateRecord($tag, $info);
            }
        });

        return ExitCode::OK;
    }

    private function updateRecord(string $tag, array $info): bool
    {
        if (!isset($this->checkedCountries[$info['cc']])) {
            $cc = TypeHelper::shouldBeString($info['cc']);
            if (Region::findOne(['id' => $cc])) {
                $this->checkedCountries[$cc] = 1;
            } else {
                Yii::warning("不明な地域コード: {$cc}@" . self::formatRecord($info), __METHOD__);
                return false;
            }
        }

        // allocation_block の登録
        if (!($block = AllocationBlock::findOne(['start_address' => $info['start']]))) {
            $block = Yii::createObject([
                'class' => AllocationBlock::class,
                'start_address' => $info['start'],
                'count' => (int)$info['count'],
                'registry_id' => $info['registry'],
                'region_id' => $info['cc'],
                'date' => $info['date'] === '00000000'
                    ? null
                    : vsprintf('%04d-%02d-%02d', [
                        (int)substr($info['date'], 0, 4),
                        (int)substr($info['date'], 4, 2),
                        (int)substr($info['date'], 6, 2),
                    ]),
            ]);
            if (!$block->save()) {
                Yii::error('AllocationBlock の新規作成に失敗: ' . self::formatRecord($info), __METHOD__);
                throw new Exception('Failed to create new allocation block row');
            }

            Yii::info('allocation_block を登録しました: ' . self::formatRecord($info), __METHOD__);
        } else {
            $dbdate = $info['date'] === '00000000'
                ? null
                : vsprintf('%04d-%02d-%02d', [
                    (int)substr($info['date'], 0, 4),
                    (int)substr($info['date'], 4, 2),
                    (int)substr($info['date'], 6, 2),
                ]);
            if (
                $block->start_address !== $info['start'] ||
                (int)$block->count !== (int)$info['count'] ||
                $block->registry_id !== $info['registry'] ||
                $block->region_id !== $info['cc'] ||
                $block->date !== $dbdate
            ) {
                // 更新
                $block->start_address = $info['start'];
                $block->count = (int)$info['count'];
                $block->registry_id = $info['registry'];
                $block->region_id = $info['cc'];
                $block->date = $dbdate;
                if (!$block->save()) {
                    Yii::error('AllocationBlock の更新に失敗: ' . self::formatRecord($info), __METHOD__);
                    throw new Exception('Could not update allocation_block row');
                }

                Yii::info('allocation_block を更新しました: ' . self::formatRecord($info), __METHOD__);
            }
        }

        // allocation_cidr の登録
        if (!$cidrs = CountToCidr::convert($block->start_address, $block->count)) {
            Yii::error('start_address,count -> cidr の変換に失敗: ' . self::formatRecord($info), __METHOD__);
            throw new Exception('Cannot convert count to cidr');
        }

        AllocationCidr::deleteAll([
            'block_id' => $block->id,
        ]);
        foreach ($cidrs as $cidrtext) {
            $cidr = Yii::createObject([
                'class' => AllocationCidr::class,
                'block_id' => $block->id,
                'cidr' => $cidrtext,
            ]);
            if (!$cidr->save()) {
                Yii::error("allocation_cidr の保存に失敗: {$cidrtext}@" . self::formatRecord($info), __METHOD__);
                throw new Exception('Cannot save allocation_cidr');
            }
        }

        return true;
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
    private function downloadAndParse(string $tag, string $url): ?array
    {
        Yii::info("Starting download from {$url}", __METHOD__);
        $t1 = microtime(true);
        $text = $this->download($url);
        if ($text === null) {
            Yii::error("ダウンロードに失敗しました: {$url}", __METHOD__);
            return null;
        }
        $t = microtime(true) - $t1;
        Yii::info(sprintf('  %.1f KiB in %.3f sec', strlen($text) / 1024, $t), __METHOD__);

        Yii::info('解析を開始します', __METHOD__);
        $ret = [];
        $offset = 0;
        $recordRegex = self::getRirStatisticsExchangeRecordFormatRegex();
        while (preg_match('/(.*?)(?:\x0d\x0a|\x0d|\x0a)/', $text, $match, 0, $offset)) {
            $offset += strlen($match[0]);
            $line = trim($match[1]);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (!preg_match($recordRegex, $line, $info)) {
                continue;
            }

            $ret[] = [
                'registry' => $info['registry'],
                'cc' => strtolower($info['cc']),
                'start' => $info['start'],
                'count' => (int)$info['count'],
                'date' => $info['date'],
                'status' => $info['status'],
            ];
        }

        if (!$ret) {
            Yii::warning('レコードがありません', __METHOD__);
            return null;
        }

        Yii::info(sprintf('  %d 個のレコード', count($ret)), __METHOD__);
        return $ret;
    }

    private function download(string $url): ?string
    {
        /** @var HttpClient $client */
        $client = Yii::createObject(HttpClient::class);
        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl($url)
            ->send();
        if (!$response->isOk) {
            return null;
        }

        return $response->getContent();
    }

    // http://www.apnic.net/publications/media-library/documents/resource-guidelines/rir-statistics-exchange-format#RecordFormat
    private static function getRirStatisticsExchangeRecordFormatRegex(): string
    {
        $registry = '(?<registry>afrinic|apnic|arin|iana|lacnic|ripencc)';
        $cc = '(?<cc>[A-Za-z]{2})';
        $type = '(?<type>ipv4)';
        $start = '(?<start>\d+\.\d+\.\d+\.\d+)';
        $count = '(?<count>\d+)';
        $date = '(?<date>\d{8})';
        $status = '(?<status>allocated|assigned)';
        return "/^{$registry}\\|{$cc}\\|{$type}\\|{$start}\\|{$count}\\|{$date}\\|{$status}/";
    }

    private static function formatRecord(array $info): string
    {
        return implode('|', [
            $info['registry'],
            $info['cc'],
            $info['start'],
            $info['count'],
            $info['date'],
            $info['status'],
        ]);
    }

    public function actionMerged(): int
    {
        $method = __METHOD__;
        return Yii::$app->db->transaction(function () use ($method): int {
            MergedCidr::deleteAll('1 = 1');

            $regions = Region::find()
                ->orderBy(['id' => SORT_ASC])
                ->all();
            foreach ($regions as $region) {
                Yii::info('CIDRをまとめる処理を開始します: ' . $region->id, $method);
                $this->updateMerged($region);
            }

            return ExitCode::OK;
        });
    }

    private function updateMerged(Region $region): void
    {
        if (!AllocationBlock::find()->andWhere(['region_id' => $region->id])->exists()) {
            Yii::warning("  {$region->id} にアドレスはありません", __METHOD__);
            return;
        }

        $cmdline = vsprintf('/usr/bin/env %s', [
            escapeshellarg(
                TypeHelper::shouldBeString(Yii::getAlias('@app/bin/filter-merge-cidr')),
            ),
        ]);
        $descriptorspec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
        ];
        if (!$process = @proc_open($cmdline, $descriptorspec, $pipes)) {
            throw new Exception('子プロセスが作成できません: ' . $cmdline);
        }

        try {
            $inCount = 0;
            $outCount = 0;
            $query = AllocationCidr::find()
                ->innerJoinWith(['block'], false)
                ->andWhere([
                    '{{%allocation_block}}.[[region_id]]' => $region->id,
                ])
                ->orderBy([
                    '{{%allocation_cidr}}.[[cidr]]' => SORT_ASC,
                ]);
            foreach ($query->asArray()->each(100) as $row) {
                ++$inCount;
                fwrite(
                    $pipes[0],
                    TypeHelper::shouldBeArray($row)['cidr'] . "\n",
                );
            }
            fclose($pipes[0]);

            while (!feof($pipes[1])) {
                $line = trim((string)fgets($pipes[1]));
                if (preg_match('!^\d+\.\d+\.\d+\.\d+/\d+$!', $line)) {
                    ++$outCount;
                    $merged = Yii::createObject([
                        'class' => MergedCidr::class,
                        'region_id' => $region->id,
                        'cidr' => $line,
                    ]);
                    if (!$merged->save()) {
                        throw new Exception('CIDR が保存できません');
                    }
                }
            }
            fclose($pipes[1]);
            proc_close($process);
            Yii::info("  {$region->id}: i:{$inCount}, o:{$outCount}", __METHOD__);
        } catch (Throwable $e) {
            @fclose($pipes[0]);
            @fclose($pipes[1]);
            @proc_close($process);
            throw $e;
        }
    }

    public function actionStat(): int
    {
        $method = __METHOD__;
        return Yii::$app->db->transaction(function () use ($method): int {
            Yii::info('統計情報を更新します', $method);

            RegionStat::deleteAll('1 = 1');

            $sql = implode(' ', [
                'INSERT INTO {{%region_stat}}',
                '([[region_id]], [[total_address_count]], [[last_allocation_date]])',
                'SELECT ' . implode(', ', [
                    '{{%allocation_block}}.[[region_id]] AS [[region_id]]',
                    'SUM({{%allocation_block}}.[[count]]) AS [[total_address_count]]',
                    'MAX({{%allocation_block}}.[[date]]) AS [[last_allocation_date]]',
                ]),
                'FROM {{%allocation_block}}',
                'GROUP BY {{%allocation_block}}.[[region_id]]',
            ]);
            Yii::$app->db->createCommand($sql)->execute();

            Yii::info('統計情報を更新しました', $method);
            return ExitCode::OK;
        });
    }

    public function actionKrfilter(): int
    {
        $query = Krfilter::find()
            ->with('regions')
            ->orderBy(['id' => SORT_ASC]);

        foreach ($query->all() as $krfilter) {
            $this->createKrfilter($krfilter, $krfilter->regions);
        }

        return ExitCode::OK;
    }

    /** @param Region[] $regions */
    private function createKrfilter(Krfilter $krfilter, array $regions): void
    {
        Yii::$app->db->transaction(function (Connection $db) use ($krfilter, $regions): void {
            $cmdline = vsprintf('/usr/bin/env %s', [
                escapeshellarg(
                    TypeHelper::shouldBeString(Yii::getAlias('@app/bin/filter-merge-cidr')),
                ),
            ]);
            $descriptorspec = [
                ['pipe', 'r'],
                ['pipe', 'w'],
            ];
            if (!$process = @proc_open($cmdline, $descriptorspec, $pipes)) {
                throw new Exception('子プロセスが作成できません: ' . $cmdline);
            }

            foreach ($regions as $region) {
                $query = $region->getMergedCidrs()
                    ->orderBy(['cidr' => SORT_ASC]);
                foreach ($query->asArray()->each(200) as $cidr) {
                    fwrite(
                        $pipes[0],
                        TypeHelper::shouldBeArray($cidr)['cidr'] . "\n",
                    );
                }
            }
            fclose($pipes[0]);

            KrfilterCidr::deleteAll(['krfilter_id' => $krfilter->id]);
            $values = [];
            while (!feof($pipes[1])) {
                $line = trim((string)fgets($pipes[1]));
                if (preg_match('!^\d+\.\d+\.\d+\.\d+/\d+$!', $line)) {
                    $values[] = [(int)$krfilter->id, $line];
                }
            }
            fclose($pipes[1]);
            $status = proc_close($process);
            if ($status !== 0) {
                throw new Exception('CIDR統合プロセスが異常終了: ' . $status . ' / ' . $cmdline);
            }

            $db->createCommand()
                ->batchInsert(KrfilterCidr::tableName(), ['krfilter_id', 'cidr'], $values)
                ->execute();
        });
    }

    public function actionIpv4bycc(): int
    {
        $method = __METHOD__;
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo'));
        return Yii::$app->db->transaction(function () use ($method, $now): int {
            Yii::info('Ipv4bycc互換形式ファイルを作成します', $method);

            $pathCidr = vsprintf('@app/web/ipv4bycc/cidr/%s/%s-cidr.txt', [
                $now->format('Y-m'),
                $now->format('Ymd'),
            ]);
            $pathMask = vsprintf('@app/web/ipv4bycc/mask/%s/%s-mask.txt', [
                $now->format('Y-m'),
                $now->format('Ymd'),
            ]);

            $tasks = [
                $pathCidr => fn () => Ipv4byccDumper::dumpCidr(),
                $pathMask => fn () => Ipv4byccDumper::dumpMask(),
            ];

            foreach ($tasks as $path => $dumper) {
                if (!$this->saveGeneratorToFile($path, $dumper)) {
                    return ExitCode::UNSPECIFIED_ERROR;
                }
            }

            return ExitCode::OK;
        });
    }

    public function actionNginxGeo(): int
    {
        $method = __METHOD__;
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo'));
        return Yii::$app->db->transaction(
            function () use ($method, $now): int {
                Yii::info('Nginxのgeo形式ファイルを作成します', $method);
                $isSuccess = $this->saveGeneratorToFile(
                    vsprintf('@app/web/nginx-geo/%s/%s.txt', [
                        $now->format('Y-m'),
                        $now->format('Ymd'),
                    ]),
                    fn () => NginxGeoDumper::dump(),
                );
                return $isSuccess ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
            },
            Transaction::REPEATABLE_READ,
        );
    }

    public function actionPreformattedGitRepo(): int
    {
        $baseDir = TypeHelper::shouldBeString(Yii::getAlias('@app/runtime/preformatted'));
        if (!FileHelper::createDirectory($baseDir, 0755, true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $enableGit = file_exists($baseDir . '/.git');
        if ($enableGit) {
            $success = self::within(
                $baseDir,
                fn (): bool => self::exec('/usr/bin/env git fetch origin --prune') === 0 &&
                    self::exec('/usr/bin/env git merge --ff-only origin/master') === 0,
            );
            if (!$success) {
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $templates = DownloadTemplate::find()
            ->with(['commentStyle', 'newline'])
            ->orderBy(['key' => SORT_ASC])
            ->all();
        $regions = Region::find()->orderBy(['id' => SORT_ASC])->all();
        foreach ($templates as $template) {
            $outDir = $baseDir . '/countries/' . $template->key;

            FileHelper::removeDirectory($outDir);
            if (!FileHelper::createDirectory($outDir, 0755, true)) {
                return ExitCode::UNSPECIFIED_ERROR;
            }

            echo $template->key . "\n";
            foreach ($regions as $region) {
                echo '.';
                $path = $outDir . '/' . $region->id . '.txt';
                if (!$this->savePreformatted($path, $region, $template)) {
                    return ExitCode::UNSPECIFIED_ERROR;
                }
            }
            echo "\n";
        }
        unset($regions);

        $krfilters = Krfilter::find()->orderBy(['id' => SORT_ASC])->all();
        foreach ($templates as $template) {
            if (str_starts_with($template->key, 'ipv4bycc-')) {
                continue;
            }
            $outDir = $baseDir . '/krfilter/' . $template->key;

            FileHelper::removeDirectory($outDir);
            if (!FileHelper::createDirectory($outDir, 0755, true)) {
                return ExitCode::UNSPECIFIED_ERROR;
            }

            echo $template->key . "\n";
            foreach ($krfilters as $krfilter) {
                echo '.';
                $path = $outDir . '/krfilter.' . $krfilter->id . '.txt';
                if (!$this->savePreformatted($path, $krfilter, $template)) {
                    return ExitCode::UNSPECIFIED_ERROR;
                }
            }
            echo "\n";
        }
        unset($krfilters);

        $pathCidr = '@app/runtime/preformatted/ipv4bycc/cidr.txt';
        $pathMask = '@app/runtime/preformatted/ipv4bycc/mask.txt';
        $tasks = [
            $pathCidr => fn () => Ipv4byccDumper::dumpCidr(false),
            $pathMask => fn () => Ipv4byccDumper::dumpMask(false),
        ];

        foreach ($tasks as $path => $dumper) {
            echo $path . "\n";
            if (!$this->saveGeneratorToFile($path, $dumper, true)) {
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $pathNginxGeo = '@app/runtime/preformatted/nginx-geo/nginx-geo.txt';
        echo $pathNginxGeo . "\n";
        if (!$this->saveGeneratorToFile($pathNginxGeo, fn () => NginxGeoDumper::dump(), true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($enableGit) {
            $success = self::within(
                $baseDir,
                function (): bool {
                    if (self::exec('/usr/bin/env git add .') !== 0) {
                        return false;
                    }

                    if (self::exec('/usr/bin/env git update-index -q --refresh') !== 0) {
                        return false;
                    }

                    echo "Checking something changed...\n";
                    exec('/usr/bin/env git diff-index --name-only HEAD --', $lines, $status);
                    if ($status !== 0) {
                        return false;
                    }

                    if (strlen(trim(implode('', $lines))) === 0) {
                        echo "Nothing changed.\n";
                        return true;
                    }

                    echo "changed\n";

                    $cmdline = vsprintf('/usr/bin/env git commit -am %s', [
                        escapeshellarg('update list'),
                    ]);
                    return self::exec($cmdline) === 0 && self::exec('/usr/bin/env git push origin master') === 0;
                },
            );
            if (!$success) {
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        return ExitCode::OK;
    }

    /**
     * @param callable(): Generator<string> $dumper
     */
    private function saveGeneratorToFile(string $path, callable $dumper, bool $overwrite = false): bool
    {
        $path = (string)Yii::getAlias($path);
        Yii::info('Create ' . basename($path), __METHOD__);

        if (!$overwrite && file_exists($path) && filesize($path) > 0) {
            Yii::info(basename($path) . ' is exists. skip.', __METHOD__);
            return true;
        }

        if (file_exists($path)) {
            @unlink($path);
        }

        try {
            if (!FileHelper::createDirectory(dirname($path), 0755, true)) {
                Yii::error('Failed to create directory ' . dirname($path), __METHOD__);
                return false;
            }
        } catch (Throwable $e) {
            Yii::error('Failed to create directory ' . dirname($path), __METHOD__);
            return false;
        }

        $tmpPath = $path . '.tmp';
        if (file_exists($tmpPath)) {
            @unlink($tmpPath);
        }

        if (!$fh = fopen($tmpPath, 'wt')) {
            return false;
        }
        foreach ($dumper() as $row) {
            fwrite($fh, $row);
        }
        fclose($fh);
        rename($tmpPath, $path);
        return true;
    }

    private function savePreformatted(
        string $path,
        Krfilter|Region $region,
        DownloadTemplate $template,
    ): bool {
        $cidrReader = function (Krfilter|Region $region): Generator {
            $it = match ($region::class) {
                Krfilter::class => krfilterCidr::find()
                    ->andWhere(['krfilter_id' => $region->id])
                    ->orderBy(['cidr' => SORT_ASC])
                    ->each(200),
                Region::class => MergedCidr::find()
                    ->andWhere(['region_id' => $region->id])
                    ->orderBy(['cidr' => SORT_ASC])
                    ->each(200),
            };

            foreach ($it as $entry) {
                yield TypeHelper::shouldBeInstanceOf(
                    $entry,
                    match ($region::class) {
                        Krfilter::class => krfilterCidr::class,
                        Region::class => MergedCidr::class,
                    },
                )->cidr;
            }
        };

        $renderer = DownloadFormatter::format(
            name: '',
            cc: match ($region::class) {
                Krfilter::class => sprintf('krfilter.%d', $region->id),
                Region::class => $region->id,
            },
            thisUrl: '',
            pageUrl: '',
            template: $template,
            isAllow: false,
            cidrList: $cidrReader($region),
            note: null,
            outputHeaders: false,
        );

        if (!$fh = fopen($path, 'w')) {
            return false;
        }

        foreach ($renderer as $line) {
            fwrite($fh, TypeHelper::shouldBeString($line));
        }
        fclose($fh);

        return true;
    }

    private function saveTimeRecord(float $startAt, float $finishAt): void
    {
        file_put_contents(
            TypeHelper::shouldBeString(
                Yii::getAlias('@app/config/params/database-update-timestamp.php'),
            ),
            implode("\n", [
                '<?php',
                '',
                'declare(strict_types=1);',
                '',
                'return [',
                vsprintf("    'startAt' => new DateTimeImmutable('@%d'),", [
                    (int)floor($startAt),
                ]),
                vsprintf("    'finishAt' => new DateTimeImmutable('@%d'),", [
                    (int)floor($finishAt),
                ]),
                vsprintf("    'took' => %f,", [
                    $finishAt - $startAt,
                ]),
                '];',
            ]) . "\n",
        );
    }

    private static function within(string $dirname, callable $callback): mixed
    {
        $oldcwd = getcwd();
        if ($oldcwd === false) {
            throw new Exception('Failed to get current working directory');
        }

        try {
            if (!chdir($dirname)) {
                throw new Exception("Failed to switch working directory to $dirname");
            }

            return $callback();
        } finally {
            chdir($oldcwd);
        }
    }

    private static function exec(string $cmdline): int
    {
        echo "Run $cmdline\n";
        exec($cmdline, $lines, $status);
        return $status;
    }
}
