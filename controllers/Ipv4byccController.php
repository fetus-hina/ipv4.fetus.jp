<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use Yii;
use app\helpers\Ipv4byccDumper;
use yii\web\Controller;
use yii\web\Response;

use function file_exists;
use function filesize;
use function preg_replace;
use function vsprintf;

final class Ipv4byccController extends Controller
{
    public function actionCidr(): Response
    {
        $today = (new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo')))
            ->setTime(0, 0, 0);
        $yesterday = $today->sub(new DateInterval('P1D'));

        return $this->proc(
            [
                vsprintf('@web/ipv4bycc/cidr/%s/%s-cidr.txt', [
                    $today->format('Y-m'),
                    $today->format('Ymd'),
                ]),
                vsprintf('@web/ipv4bycc/cidr/%s/%s-cidr.txt', [
                    $yesterday->format('Y-m'),
                    $yesterday->format('Ymd'),
                ]),
            ],
            fn () => Ipv4byccDumper::dumpCidr(),
        );
    }

    public function actionMask(): Response
    {
        $today = (new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo')))
            ->setTime(0, 0, 0);
        $yesterday = $today->sub(new DateInterval('P1D'));

        return $this->proc(
            [
                vsprintf('@web/ipv4bycc/mask/%s/%s-mask.txt', [
                    $today->format('Y-m'),
                    $today->format('Ymd'),
                ]),
                vsprintf('@web/ipv4bycc/mask/%s/%s-mask.txt', [
                    $yesterday->format('Y-m'),
                    $yesterday->format('Ymd'),
                ]),
            ],
            fn () => Ipv4byccDumper::dumpMask(),
        );
    }

    /**
     * @param string[] $paths
     * @param callable(): Generator $dumper
     */
    private function proc(array $paths, callable $dumper): Response
    {
        foreach ($paths as $path) {
            $localPath = (string)Yii::getAlias((string)preg_replace('!^@web/!', '@app/web/', $path));
            if (file_exists($localPath) && filesize($localPath)) {
                return $this->redirect($path);
            }
        }

        $resp = Yii::$app->response;
        $resp->format = Response::FORMAT_RAW;
        $resp->charset = 'UTF-8';
        $resp->headers->set('Content-Type', 'text/plain');
        $resp->stream = $dumper;
        return $resp;
    }
}
