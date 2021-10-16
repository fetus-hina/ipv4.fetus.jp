<?php

declare(strict_types=1);

namespace app\commands;

use Exception;
use Throwable;
use Yii;
use app\helpers\CountToCidr;
use app\models\AllocationBlock;
use app\models\AllocationCidr;
use app\models\Krfilter;
use app\models\KrfilterCidr;
use app\models\MergedCidr;
use app\models\Region;
use app\models\RegionStat;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\httpclient\Client as HttpClient;

class UpdateController extends Controller
{
    private const TAG_AFRINIC = 'afrinic';
    private const TAG_APNIC   = 'apnic';
    private const TAG_ARIN    = 'arin';
    private const TAG_LACNIC  = 'lacnic';
    private const TAG_RIPENCC = 'ripe-ncc';

    private const URL_AFRINIC = 'http://ftp.afrinic.net/stats/afrinic/delegated-afrinic-extended-latest';
    private const URL_APNIC   = 'http://ftp.apnic.net/stats/apnic/delegated-apnic-extended-latest';
    private const URL_ARIN    = 'http://ftp.arin.net/pub/stats/arin/delegated-arin-extended-latest';
    // private const URL_IANA    = 'http://ftp.apnic.net/stats/iana/delegated-iana-latest';
    private const URL_LACNIC  = 'http://ftp.lacnic.net/pub/stats/lacnic/delegated-lacnic-extended-latest';
    private const URL_RIPENCC = 'http://ftp.ripe.net/pub/stats/ripencc/delegated-ripencc-extended-latest';

    // const KRFILTER_PATH = 'application.data.krfilter';
    // const KRFILTER_1 = 'cn,kr,kp';
    // const KRFILTER_2 = 'cn,hk,id,in,kp,kr,tw';
    // const KRFILTER_3 = 'cn,hk,id,in,kp,kr,ru,tw';
    // const KRFILTER_4 = 'eu,at,ax,be,bg,cy,cz,de,dk,ee,es,fi,fr,gb,gf,gi,gp,gr,hr,hu,ie,it,li,lt,lu,lv,mf,mq,mt,nl,no,pl,pt,re,ro,se,si,sk,uk,yt';

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
        /** @var int */
        $status = ExitCode::OK;

        /** @var ?float */
        $updateStartAt = null;

        /** @var ?float */
        $updateFinishAt = null;

        if (!$skipUpdate) {
            try {
                $updateStartAt = microtime(true);

                Yii::$app->db->transaction(function (): void {
                    Yii::info('Deleting allocation_cidr', __METHOD__);
                    AllocationCidr::deleteAll('1 = 1');

                    Yii::info('Deleting allocation_block', __METHOD__);
                    AllocationBlock::deleteAll('1 = 1');

                    /** @var array<string, callable> */
                    $actions = [
                        'AfriNIC'   => fn () => $this->actionAfrinic(),
                        'APNIC'     => fn () => $this->actionApnic(),
                        'ARIN'      => fn () => $this->actionArin(),
                        'LACNIC'    => fn () => $this->actionLacnic(),
                        'RIPE NCC'  => fn () => $this->actionRipeNcc(),
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
        $updateFinishAt = microtime(true);

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
        return $this->update(static::TAG_AFRINIC, static::URL_AFRINIC);
    }

    public function actionApnic(): int
    {
        return $this->update(static::TAG_APNIC, static::URL_APNIC);
    }

    public function actionArin(): int
    {
        return $this->update(static::TAG_ARIN, static::URL_ARIN);
    }

    public function actionLacnic(): int
    {
        return $this->update(static::TAG_LACNIC, static::URL_LACNIC);
    }

    public function actionRipeNcc(): int
    {
        return $this->update(static::TAG_RIPENCC, static::URL_RIPENCC);
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
            if (Region::findOne(['id' => $info['cc']])) {
                $this->checkedCountries[$info['cc']] = 1;
            } else {
                Yii::warning("不明な地域コード: {$info['cc']}@" . static::formatRecord($info), __METHOD__);
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
                Yii::error('AllocationBlock の新規作成に失敗: ' . static::formatRecord($info), __METHOD__);
                throw new Exception('Failed to create new allocation block row');
            }

            Yii::info('allocation_block を登録しました: ' . static::formatRecord($info), __METHOD__);
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
                    Yii::error('AllocationBlock の更新に失敗: ' . static::formatRecord($info), __METHOD__);
                    throw new Exception('Could not update allocation_block row');
                }

                Yii::info('allocation_block を更新しました: ' . static::formatRecord($info), __METHOD__);
            }
        }

        // allocation_cidr の登録
        if (!$cidrs = CountToCidr::convert($block->start_address, $block->count)) {
            Yii::error('start_address,count -> cidr の変換に失敗: ' . static::formatRecord($info), __METHOD__);
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
                Yii::error("allocation_cidr の保存に失敗: {$cidrtext}@" . static::formatRecord($info), __METHOD__);
                throw new Exception('Cannot save allocation_cidr');
            }
        }

        return true;
    }

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
        $recordRegex = static::getRirStatisticsExchangeRecordFormatRegex();
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
                'registry'  => $info['registry'],
                'cc'        => strtolower($info['cc']),
                'start'     => $info['start'],
                'count'     => (int)$info['count'],
                'date'      => $info['date'],
                'status'    => $info['status'],
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
        /** @var HttpClient */
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
        $registry   = '(?<registry>afrinic|apnic|arin|iana|lacnic|ripencc)';
        $cc         = '(?<cc>[A-Za-z]{2})';
        $type       = '(?<type>ipv4)';
        $start      = '(?<start>\d+\.\d+\.\d+\.\d+)';
        $count      = '(?<count>\d+)';
        $date       = '(?<date>\d{8})';
        $status     = '(?<status>allocated|assigned)';
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
            escapeshellarg(Yii::getAlias('@app/bin/filter-merge-cidr')),
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
                fwrite($pipes[0], $row['cidr'] . "\n");
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

    /** @var Region[] $regions */
    private function createKrfilter(Krfilter $krfilter, array $regions): void
    {
        Yii::$app->db->transaction(function (Connection $db) use ($krfilter, $regions): void {
            $cmdline = vsprintf('/usr/bin/env %s', [
                escapeshellarg(Yii::getAlias('@app/bin/filter-merge-cidr')),
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
                    fwrite($pipes[0], $cidr['cidr'] . "\n");
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

    private function saveTimeRecord(float $startAt, float $finishAt): void
    {
        file_put_contents(
            Yii::getAlias('@app/config/params/database-update-timestamp.php'),
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
            ]) . "\n"
        );
    }
}
