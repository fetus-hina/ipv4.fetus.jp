<?php

declare(strict_types=1);

namespace app\controllers;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Generator;
use Yii;
use app\helpers\IPHelper;
use app\helpers\TypeHelper;
use app\models\MergedCidr;
use app\models\Region;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

final class Ipv4byccController extends Controller
{
    public function actionCidr(): Response
    {
        return $this->proc(
            Url::to(['ipv4bycc/cidr'], true),
            fn (MergedCidr $model): string => $model->cidr,
        );
    }

    public function actionMask(): Response
    {
        $masks = array_combine(
            range(1, 32),
            array_map(
                fn (int $bits): string => (string)long2ip((int)IPHelper::bitmask($bits)),
                range(1, 32),
            ),
        );

        return $this->proc(
            Url::to(['ipv4bycc/mask'], true),
            function (MergedCidr $model) use ($masks): string {
                $cidr = $model->cidr;
                $pos = strpos($cidr, '/');
                if ($pos === false) {
                    throw new ServerErrorHttpException();
                }
                return vsprintf('%s/%s', [
                    substr($cidr, 0, $pos),
                    $masks[(int)substr($cidr, $pos + 1)],
                ]);
            },
        );
    }

    /**
     * @param callable(MergedCidr): string $formatter
     */
    private function proc(string $url, callable $formatter): Response
    {
        $resp = Yii::$app->response;
        $resp->format = Response::FORMAT_RAW;
        $resp->charset = 'UTF-8';
        $resp->headers->set('Content-Type', 'text/plain');
        $resp->stream = function () use ($url, $formatter): Generator {
            foreach ($this->getHeaders($url) as $header) {
                yield rtrim('# ' . $header) . "\n";
            }
            yield "\n";

            $regions = Region::find()->orderBy(['id' => SORT_ASC])->all();
            foreach ($regions as $region) {
                $cidrs = MergedCidr::find()
                    ->andWhere(['region_id' => $region->id])
                    ->orderBy(['cidr' => SORT_ASC]);
                foreach ($cidrs->each(200) as $cidr) {
                    yield vsprintf("%s\t%s\n", [
                        strtoupper($region->id), // JP
                        // 127.0.0.0/8 or 127.0.0.0/255.0.0.0
                        $formatter(TypeHelper::shouldBeInstanceOf($cidr, MergedCidr::class)),
                    ]);
                }
            }
        };

        return $resp;
    }

    /**
     * @return string[]
     */
    private function getHeaders(string $url): array
    {
        $now = (new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone)))
            ->setTimestamp((int)$_SERVER['REQUEST_TIME']);

        return [
            '',
            Url::to(['site/index'], true),
            '',
            '「世界の国別 IPv4 アドレス割り当てリスト」互換形式',
            $url,
            '',
            vsprintf('出力日時: %s (%s)', [
                $now->format('Y-m-d H:i:s T'),
                $now->setTimezone(new DateTimeZone('Etc/UTC'))->format(DateTimeInterface::ATOM),
            ]),
            '',
            '自動化したアクセスについて:',
            '  ' . Url::to(['site/about', '#' => 'automation'], true),
            '',
        ];
    }
}
