<?php

declare(strict_types=1);

namespace app\controllers;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use Yii;
use app\models\Krfilter;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class KrfilterController extends Controller
{
    public function actionView(): string
    {
        return $this->render('view');
    }

    public function actionPlain(int $id, ?string $template = null): void
    {
        $krfilter = Krfilter::findOne(['id' => $id]);
        if (!$krfilter) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if ($template !== null) {
            if (
                $template !== 'apache' &&
                $template !== 'apache24' &&
                $template !== 'ipsecurity' &&
                $template !== 'iptables' &&
                $template !== 'nginx' &&
                $template !== 'nginx-geo' &&
                $template !== 'postfix'
            ) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }
        }

        Yii::$app->db->transaction(function () use ($krfilter, $template): void {
            $resp = Yii::$app->response;
            $resp->format = Response::FORMAT_RAW;
            $resp->charset = 'UTF-8';
            $resp->headers->set('Content-Type', 'text/plain');
            $resp->stream = fn() => $this->putPlainData($krfilter, $template);
        });
    }

    private function putPlainData(Krfilter $krfilter, ?string $template): Generator
    {
        foreach ($this->createPlainHeaders($krfilter, $template) as $line) {
            yield $line . "\n";
        }

        yield "\n";

        $formatter = (function () use ($template): callable {
            switch ($template) {
                case 'apache':
                    return fn($v) => "deny from {$v}";

                case 'apache24':
                    return fn($v) => "Require not ip {$v}";

                case 'ipsecurity':
                    return fn($v) => vsprintf('  <add allowed="false" ipAddress="%s" subnetMask="%s" />', [
                        Html::encode(static::extractIP($v)),
                        Html::encode(static::subnetMask($v)),
                    ]);

                case 'iptables':
                    return fn($v) => sprintf(
                        '-A RULE1 -s %s -j RULE2',
                        substr((string)$v . '                  ', 0, 18),
                    );

                case 'nginx':
                    return fn($v) => "deny {$v};";

                case 'nginx-geo':
                    return fn($v) => "  {$v} 1;";

                case 'postfix':
                    return fn($v) => sprintf('%s REJECT', substr($v . '                    ', 0, 20));
            }

            return fn($v) => (string)$v;
        })();

        if ($template === 'nginx-geo') {
            yield "geo \$ipv4_krfilter_{$krfilter->id} {\n";
            yield "  default 0;\n";
        } elseif ($template === 'ipsecurity') {
            yield '<ipSecurity allowUnlisted="true">' . "\n";
        }

        $query = $krfilter->getKrfilterCidrs()
            ->asArray()
            ->orderBy(['cidr' => SORT_ASC]);
        foreach ($query->each(500) as $row) {
            yield $formatter($row['cidr']) . "\n";
        }

        if ($template === 'nginx-geo') {
            yield "}\n";
        } elseif ($template === 'ipsecurity') {
            yield "</ipSecurity>\n";
        }
    }

    private function createPlainHeaders(Krfilter $krfilter, ?string $template): Generator
    {
        $now = (new DateTimeImmutable())
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()))
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone));

        $commentBlockPrefix = '';
        $commentBlockSuffix = '';
        $commentPrefix = '# ';
        $commentSuffix = '';

        $values = [
            '',
            $krfilter->name,
            vsprintf(' %s', [
                Url::to(
                    ['krfilter/plain',
                        'id' => $krfilter->id,
                        'template' => $template,
                    ],
                    true
                ),
            ]),
            vsprintf(' 出力日時: %s (%s)', [
                $now->format('Y-m-d H:i:s T'),
                $now->setTimeZone(new DateTimeZone('Etc/UTC'))->format(DateTime::ATOM),
            ]),
            '',
            '  次の国や地域が統合されて出力されています:',
        ];

        $regions = $krfilter->regions;
        usort($regions, fn($a, $b) => strcmp($a->id, $b->id));
        $perLine = (int)floor(
            ((72 + strlen(', ')) - (strlen($commentPrefix) + strlen($commentSuffix) + 4)) / strlen('kr, ')
        );
        for ($i = 0; $i < count($regions); $i += $perLine) {
            $values[] = '    ' . implode(', ', array_map(
                fn($region) => $region->id,
                array_slice($regions, $i, $perLine),
            ));
        }
        $values[] = '';

        switch ($template) {
            case 'ipsecurity':
                $commentBlockPrefix = '<!--';
                $commentBlockSuffix = '-->';
                break;

            case 'iptables':
                $values[] = 'Usage: RULE1 を "INPUT" などに、RULE2 を "ACCEPT" や "DROP" に置き換えて利用してください';
                $values[] = '';
                break;

            case 'postfix':
                $values[] = 'Usage: smtpd_client_restrictions などに';
                $values[] = '「check_client_access cidr:/path/to/file」のように指定します';
                $values[] = '';
                break;
        }

        if ($commentBlockPrefix != '') {
            yield $commentBlockPrefix;
        }
        foreach ($values as $line) {
            yield rtrim($commentPrefix . $line . $commentSuffix);
        }
        if ($commentBlockSuffix != '') {
            yield $commentBlockSuffix;
        }
    }

    private static function extractIP(string $cidr): string
    {
        return preg_replace('!/\d+$!', '', $cidr);
    }

    private static function subnetMask(string $cidr): string
    {
        $prefix = (int)preg_replace('!^.+?/!', '', $cidr);
        $maskBin = (0xffffffff << (32 - $prefix)) & 0xffffffff;
        return long2ip($maskBin);
    }
}
