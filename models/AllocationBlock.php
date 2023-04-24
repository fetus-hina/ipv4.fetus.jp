<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%allocation_block}}".
 *
 * @property int $id
 * @property int $count
 * @property string|null $date
 * @property string $region_id
 * @property string $registry_id
 * @property string $start_address
 *
 * @property AllocationCidr[] $allocationCidrs
 * @property ?Region $region
 * @property ?Registry $registry
 */
final class AllocationBlock extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%allocation_block}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['start_address', 'count', 'registry_id', 'region_id'], 'required'],
            [['start_address'], 'string'],
            [['count'], 'integer'],
            [['date'], 'safe'],
            [['registry_id'], 'string',
                'max' => 7,
            ],
            [['region_id'], 'string',
                'max' => 2,
            ],
            [['start_address'], 'unique'],
            [['region_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Region::class,
                'targetAttribute' => [
                    'region_id' => 'id',
                ],
            ],
            [['registry_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Registry::class,
                'targetAttribute' => [
                    'registry_id' => 'id',
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
            'id' => 'ID',
            'count' => 'Count',
            'date' => 'Date',
            'region_id' => 'Region ID',
            'registry_id' => 'Registry ID',
            'start_address' => 'Start Address',
        ];
    }

    public function getAllocationCidrs(): ActiveQuery
    {
        return $this->hasMany(AllocationCidr::class, ['block_id' => 'id']);
    }

    public function getRegion(): ActiveQuery
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }

    public function getRegistry(): ActiveQuery
    {
        return $this->hasOne(Registry::class, ['id' => 'registry_id']);
    }
}
