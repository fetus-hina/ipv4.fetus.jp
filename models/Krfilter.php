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
     * @inheritdoc
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

    /**
     * @return ActiveQuery<KrfilterCidr>
     */
    public function getKrfilterCidrs(): ActiveQuery
    {
        return $this->hasMany(KrfilterCidr::class, ['krfilter_id' => 'id']);
    }

    /**
     * @return ActiveQuery<KrfilterRegion>
     */
    public function getKrfilterRegions(): ActiveQuery
    {
        return $this->hasMany(KrfilterRegion::class, ['krfilter_id' => 'id']);
    }

    /**
     * @return ActiveQuery<Region>
     */
    public function getRegions(): ActiveQuery
    {
        return $this->hasMany(Region::class, ['id' => 'region_id'])
            ->via('krfilterRegions');
    }
}
