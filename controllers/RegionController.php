<?php

declare(strict_types=1);

namespace app\controllers;

use Generator;
use Yii;
use app\helpers\DownloadFormatter;
use app\helpers\TypeHelper;
use app\models\DownloadTemplate;
use app\models\Region;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class RegionController extends Controller
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

        if (
            $control !== null &&
            $control !== 'allow' &&
            $control !== 'deny'
        ) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        Yii::$app->db->transaction(function () use ($region, $template, $control, $templateModel): void {
            $resp = Yii::$app->response;
            $resp->format = Response::FORMAT_RAW;
            $resp->charset = 'UTF-8';
            $resp->headers->set('Content-Type', 'text/plain');
            $resp->stream = fn (): Generator => DownloadFormatter::format(
                vsprintf('[%s] %s (%s)', [
                    $region->id,
                    $region->name_ja,
                    $region->name_en,
                ]),
                $region->id,
                ['region/plain', 'cc' => $region->id, 'template' => $template, 'control' => $control],
                ['region/view', 'cc' => $region->id],
                $templateModel,
                $control !== null
                    ? ($control === 'allow')
                    : ($region->id === 'jp'),
                (function () use ($region) {
                    $query = $region->getMergedCidrs()
                        ->asArray()
                        ->orderBy(['cidr' => SORT_ASC]);
                    foreach ($query->each(500) as $row) {
                        yield TypeHelper::shouldBeString(
                            TypeHelper::shouldBeArray(
                                $row,
                                TypeHelper::ARRAY_ASSOC,
                            )['cidr']
                        );
                    }
                })(),
                null,
            );
        });
    }
}
