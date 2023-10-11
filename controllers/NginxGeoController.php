<?php

declare(strict_types=1);

namespace app\controllers;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use Yii;
use app\helpers\NginxGeoDumper;
use yii\web\Controller;
use yii\web\Response;

use function file_exists;
use function filesize;
use function preg_replace;
use function vsprintf;

final class NginxGeoController extends Controller
{
    public function actionIndex(): Response
    {
        $today = (new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo')))
            ->setTime(0, 0, 0);
        $yesterday = $today->sub(new DateInterval('P1D'));

        return $this->proc(
            [
                vsprintf('@web/nginx-geo/%s/%s.txt', [
                    $today->format('Y-m'),
                    $today->format('Ymd'),
                ]),
                vsprintf('@web/nginx-geo/%s/%s.txt', [
                    $yesterday->format('Y-m'),
                    $yesterday->format('Ymd'),
                ]),
            ],
            fn () => NginxGeoDumper::dump(),
        );
    }

    /**
     * @param string[] $paths
     * @param callable(): Generator<string> $dumper
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
