<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%krfilter}}".
 *
 * @property int $id
 * @property string $name
 *
 * @property KrfilterCidr[] $krfilterCidrs
 * @property KrfilterRegion[] $krfilterRegions
 * @property Region[] $regions
 */
final class Krfilter extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%krfilter}}';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string',
                'max' => 255,
            ],
            [['id'], 'unique'],
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
            'name' => 'Name',
        ];
    }

    public function getKrfilterCidrs(): ActiveQuery
    {
        return $this->hasMany(KrfilterCidr::class, ['krfilter_id' => 'id']);
    }

    public function getKrfilterRegions(): ActiveQuery
    {
        return $this->hasMany(KrfilterRegion::class, ['krfilter_id' => 'id']);
    }

    public function getRegions(): ActiveQuery
    {
        return $this->hasMany(Region::class, ['id' => 'region_id'])
            ->via('krfilterRegions');
    }
}
