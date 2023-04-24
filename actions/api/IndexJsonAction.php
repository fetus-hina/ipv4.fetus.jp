<?php

declare(strict_types=1);

namespace app\actions\api;

use Yii;
use app\helpers\TypeHelper;
use app\models\Region;
use app\models\RegionStat;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function date;
use function strtotime;

use const SORT_ASC;

final class IndexJsonAction extends Action
{
    public function run(): Response
    {
        $resp = Yii::$app->response;
        $resp->format = Response::FORMAT_JSON;
        $resp->data = Yii::$app->db->transaction(
            fn (Connection $db): array => ArrayHelper::map(
                RegionStat::find()
                    ->with('region')
                    ->andWhere(['and',
                        ['>', '{{%region_stat}}.[[total_address_count]]', 0],
                        ['not', ['{{%region_stat}}.[[last_allocation_date]]' => null]],
                    ])
                    ->orderBy(['{{%region_stat}}.[[region_id]]' => SORT_ASC])
                    ->all($db),
                'region_id',
                fn (RegionStat $model): array => [
                    'cc' => $model->region_id,
                    'name' => [
                        'ja' => TypeHelper::shouldBeInstanceOf($model->region, Region::class)->name_ja,
                        'en' => TypeHelper::shouldBeInstanceOf($model->region, Region::class)->name_en,
                    ],
                    'count' => (int)$model->total_address_count,
                    'updated' => date(
                        'Y-m-d',
                        (int)strtotime((string)$model->last_allocation_date),
                    ),
                ],
            ),
            Transaction::READ_COMMITTED,
        );
        return $resp;
    }
}
