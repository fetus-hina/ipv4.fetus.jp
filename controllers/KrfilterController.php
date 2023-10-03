<?php

declare(strict_types=1);

namespace app\controllers;

use Generator;
use Yii;
use app\helpers\DownloadFormatter;
use app\helpers\TypeHelper;
use app\helpers\Unicode;
use app\models\DownloadTemplate;
use app\models\Krfilter;
use app\models\Region;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_map;
use function array_slice;
use function count;
use function floor;
use function implode;
use function sprintf;
use function strcmp;
use function strlen;
use function usort;
use function vsprintf;

use const SORT_ASC;

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

        $templateModel = $template !== null
            ? DownloadTemplate::find()
                ->andWhere([
                    'can_use_in_url' => true,
                    'key' => $template,
                ])
                ->one()
            : DownloadTemplate::findOne(['key' => 'plain']);
        if (!$templateModel) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        Yii::$app->db->transaction(function () use ($krfilter, $template, $templateModel): void {
            $resp = Yii::$app->response;
            $resp->format = Response::FORMAT_RAW;
            $resp->charset = 'UTF-8';
            $resp->headers->set('Content-Type', 'text/plain');
            $resp->stream = fn (): Generator => DownloadFormatter::format(
                $krfilter->name,
                sprintf('krfilter_%d', $krfilter->id),
                ['krfilter/plain', 'id' => $krfilter->id, 'template' => $template],
                ['krfilter/view'],
                $templateModel,
                false, // this is denylist
                (function () use ($krfilter): Generator {
                    $query = $krfilter->getKrfilterCidrs()
                        ->asArray()
                        ->orderBy(['cidr' => SORT_ASC]);
                    foreach ($query->each(500) as $row) {
                        yield TypeHelper::shouldBeString(
                            TypeHelper::shouldBeArray(
                                $row,
                                TypeHelper::ARRAY_ASSOC,
                            )['cidr'],
                        );
                    }
                })(),
                (function () use ($krfilter): string {
                    $lines = [
                        '次の国や地域が統合されて出力されています:',
                        'Merged the following countries and regions:',
                    ];

                    $regions = $krfilter->regions;
                    usort($regions, fn (Region $a, Region $b): int => strcmp($a->id, $b->id));
                    $perLine = (int)floor(
                        (72 + strlen(', ') - (strlen('# ') + 2)) / strlen('XX kr, '),
                    );
                    for ($i = 0; $i < count($regions); $i += $perLine) {
                        $lines[] = '  ' . implode(', ', array_map(
                            fn (Region $region): string => vsprintf('%s %s', [
                                Unicode::asciiToRegionalIndicator($region->id),
                                $region->id,
                            ]),
                            array_slice($regions, $i, $perLine),
                        ));
                    }
                    return implode("\n", $lines);
                })(),
            );
        });
    }
}
