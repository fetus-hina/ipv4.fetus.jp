<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\helpers\DownloadFormatter;
use app\models\DownloadTemplate;
use app\models\Krfilter;
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
            $resp->stream = fn () => DownloadFormatter::format(
                $krfilter->name,
                sprintf('krfilter_%d', $krfilter->id),
                ['krfilter/plain', 'id' => $krfilter->id, 'template' => $template],
                ['krfilter/view'],
                $templateModel,
                false, // this is denylist
                (function () use ($krfilter) {
                    $query = $krfilter->getKrfilterCidrs()
                        ->asArray()
                        ->orderBy(['cidr' => SORT_ASC]);
                    foreach ($query->each(500) as $row) {
                        yield (string)$row['cidr'];
                    }
                })(),
                (function () use ($krfilter) {
                    $lines = ['次の国や地域が統合されて出力されています:'];

                    $regions = $krfilter->regions;
                    usort($regions, fn ($a, $b) => strcmp($a->id, $b->id));
                    $perLine = (int)floor(
                        (72 + strlen(', ') - (strlen('# ') + 2)) / strlen('kr, ')
                    );
                    for ($i = 0; $i < count($regions); $i += $perLine) {
                        $lines[] = '  ' . implode(', ', array_map(
                            fn ($region) => $region->id,
                            array_slice($regions, $i, $perLine),
                        ));
                    }
                    return implode("\n", $lines);
                })(),
            );
        });
    }
}
