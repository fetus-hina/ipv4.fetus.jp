<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%merged_cidr}}".
 *
 * @property int $id
 * @property string $cidr
 * @property string $region_id
 *
 * @property ?Region $region
 */
final class MergedCidr extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%merged_cidr}}';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['region_id', 'cidr'], 'required'],
            [['cidr'], 'string'],
            [['region_id'], 'string',
                'max' => 2,
            ],
            [['region_id', 'cidr'], 'unique',
                'skipOnEmpty' => true,
                'skipOnError' => true,
                'targetAttribute' => [
                    'region_id',
                    'cidr',
                ],
            ],
            [['cidr'], 'unique'],
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
            'id' => 'ID',
            'cidr' => 'Cidr',
            'region_id' => 'Region ID',
        ];
    }

    public function getRegion(): ActiveQuery
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }
}
