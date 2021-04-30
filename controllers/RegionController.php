<?php

declare(strict_types=1);

namespace app\controllers;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use Yii;
use app\models\Region;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RegionController extends Controller
{
    public function actionView(string $cc): string
    {
        $region = Region::findOne(['id' => $cc]);
        if (!$region || !$region->regionStats) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return $this->render('view', [
            'region' => $region,
        ]);
    }

    public function actionPlain(
        string $cc,
        ?string $template = null,
        ?string $control = null
    ): void {
        $region = Region::findOne(['id' => $cc]);
        if (!$region || !$region->regionStats) {
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

        if (
            $control !== null &&
            $control !== 'allow' &&
            $control !== 'deny'
        ) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        Yii::$app->db->transaction(function () use ($region, $template, $control): void {
            $resp = Yii::$app->response;
            $resp->format = Response::FORMAT_RAW;
            $resp->charset = 'UTF-8';
            $resp->headers->set('Content-Type', 'text/plain');
            $resp->stream = fn() => $this->putPlainData($region, $template, $control);
        });
    }

    private function putPlainData(
        Region $region,
        ?string $template,
        ?string $control
    ): Generator {
        foreach ($this->createPlainHeaders($region, $template, $control) as $line) {
            yield $line . "\n";
        }

        yield "\n";

        if ($control !== 'allow' && $control !== 'deny') {
            $control = ($region->id === 'jp') ? 'allow' : 'deny';
        }
        assert($control === 'allow' || $control === 'deny'); // @phpstan-ignore-line

        $formatter = (function () use ($template, $control): callable {
            switch ($template) {
                case 'apache':
                    return ($control === 'allow')
                        ? fn($v) => "allow from {$v}"
                        : fn($v) => "deny from {$v}";

                case 'apache24':
                    return ($control === 'allow')
                        ? fn($v) => "Require ip {$v}"
                        : fn($v) => "Require not ip {$v}";

                case 'ipsecurity':
                    return fn($v) => vsprintf('  <add allowed="%s" ipAddress="%s" subnetMask="%s" />', [
                        ($control === 'allow') ? 'true' : 'false',
                        Html::encode(static::extractIP($v)),
                        Html::encode(static::subnetMask($v)),
                    ]);

                case 'iptables':
                    return fn($v) => sprintf(
                        '-A RULE1 -s %s -j RULE2',
                        substr((string)$v . '                  ', 0, 18),
                    );

                case 'nginx':
                    return ($control === 'allow')
                        ? fn($v) => "allow {$v};"
                        : fn($v) => "deny {$v};";

                case 'nginx-geo':
                    return fn($v) => "  {$v} 1;";

                case 'postfix':
                    return fn($v) => sprintf(
                        '%s %s',
                        substr($v . '                    ', 0, 20),
                        $control === 'allow' ? 'OK' : 'REJECT',
                    );
            }

            return fn($v) => (string)$v;
        })();

        if ($template === 'nginx-geo') {
            yield "geo \$ipv4_{$region->id} {\n";
            yield "  default 0;\n";
        } elseif ($template === 'ipsecurity') {
            yield vsprintf('<ipSecurity allowUnlisted="%s">', [
                ($control === 'allow') ? 'false' : 'true',
            ]) . "\n";
        }

        $query = $region->getMergedCidrs()
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

    private function createPlainHeaders(
        Region $region,
        ?string $template,
        ?string $control
    ): Generator {
        $now = (new DateTimeImmutable())
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()))
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone));

        $commentBlockPrefix = '';
        $commentBlockSuffix = '';
        $commentPrefix = '# ';
        $commentSuffix = '';

        $values = [
            '',
            vsprintf('[%s] %s (%s)', [
                $region->id,
                $region->name_ja,
                $region->name_en,
            ]),
            vsprintf(' %s', [
                Url::to(
                    ['region/plain',
                        'cc' => $region->id,
                        'template' => $template,
                        'control' => $control,
                    ],
                    true
                ),
            ]),
            vsprintf(' 出力日時: %s (%s)', [
                $now->format('Y-m-d H:i:s T'),
                $now->setTimeZone(new DateTimeZone('Etc/UTC'))->format(DateTime::ATOM),
            ]),
            '',
        ];

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
