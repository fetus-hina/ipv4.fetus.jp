<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%region_stat}}".
 *
 * @property string|null $last_allocation_date
 * @property string $region_id
 * @property int $total_address_count
 *
 * @property ?Region $region
 */
final class RegionStat extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%region_stat}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['region_id', 'total_address_count'], 'required'],
            [['total_address_count'], 'integer'],
            [['last_allocation_date'], 'safe'],
            [['region_id'], 'string',
                'max' => 2,
            ],
            [['region_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Region::class,
                'targetAttribute' => [
                    'region_id' => 'id',
                ],
            ],
        ];
    }

    /**
     * @codeCoverageIgnore
     * @return array<string, string>
     */
    public function attributeLabels()
    {
        return [
            'last_allocation_date' => 'Last Allocation Date',
            'region_id' => 'Region ID',
            'total_address_count' => 'Total Address Count',
        ];
    }

    public function getRegion(): ActiveQuery
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }
}
