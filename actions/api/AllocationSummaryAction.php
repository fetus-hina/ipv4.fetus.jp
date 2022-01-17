<?php

declare(strict_types=1);

namespace app\actions\api;

use Yii;
use app\helpers\TypeHelper;
use app\models\RegionStat;
use yii\base\Action;
use yii\web\Response;

final class AllocationSummaryAction extends Action
{
    private const TOTAL_ADDRESS_SPACE = 1 << 32;
    private const RESERVED_IP_ADDRESSES = 592715776;

    private const ID_FREE = 'free';
    private const ID_OTHERS = 'others';
    private const ID_RESERVED = 'reserved';

    public function run(): Response
    {
        $resp = Yii::$app->response;
        $resp->format = Response::FORMAT_JSON;
        $resp->data = $this->createJson();
        return $resp;
    }

    private function createJson(): array
    {
        // 割り振りの多い国のリスト
        $manyAllocCountries = $this->getManyAllocCountries(
            (int)floor(self::TOTAL_ADDRESS_SPACE * 0.02) // 2%
        );

        // 全割り振り数
        $totalAlloc = $this->getAllocatedAddressCount();

        // 「割り振りの多い国のリスト」の合計割り振り数
        $manyAlloc = array_reduce(
            array_map(
                fn (RegionStat $model): int => $model->total_address_count,
                $manyAllocCountries,
            ),
            fn (int $carry, int $value): int => $carry + $value,
            0,
        );

        return array_values(
            array_map(
                fn (array $data) => array_merge(
                    $data,
                    [
                        'rate' => (float)$data['count'] / (float)self::TOTAL_ADDRESS_SPACE,
                    ],
                ),
                array_merge(
                    array_map(
                        fn (RegionStat $model): array => [
                            'cc' => $model->region_id,
                            'name' => $model->region?->formattedName ?? $model->region_id,
                            'count' => $model->total_address_count,
                        ],
                        $manyAllocCountries,
                    ),
                    [
                        [
                            'cc' => self::ID_OTHERS,
                            'name' => Yii::t('app', 'Others'),
                            'count' => $totalAlloc - $manyAlloc,
                        ],
                        [
                            'cc' => self::ID_FREE,
                            'name' => Yii::t('app', 'Not Allocated'),
                            'count' => $this->getFreeAddressCount(),
                        ],
                        [
                            'cc' => self::ID_RESERVED,
                            'name' => Yii::t('app', 'Reserved'),
                            'count' => self::RESERVED_IP_ADDRESSES,
                        ],
                    ],
                ),
            ),
        );
    }

    /** @return RegionStat[] */
    private function getManyAllocCountries(int $threshold): array
    {
        return RegionStat::find()
            ->with('region')
            ->andWhere(['>=', 'total_address_count', $threshold])
            ->orderBy([
                'total_address_count' => SORT_DESC,
                'region_id' => SORT_ASC,
            ])
            ->all();
    }

    private function getFreeAddressCount(): int
    {
        return self::TOTAL_ADDRESS_SPACE
            - self::RESERVED_IP_ADDRESSES
            - $this->getAllocatedAddressCount();
    }

    private function getAllocatedAddressCount(): int
    {
        static $cache = null;
        if ($cache === null) {
            $cache = TypeHelper::shouldBeInteger(
                filter_var(
                    RegionStat::find()->sum('total_address_count'),
                    FILTER_VALIDATE_INT,
                ),
            );
        }
        return $cache;
    }
}
