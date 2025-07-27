<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\models\traits\RegionNameTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%region}}".
 *
 * @property string $id
 * @property string $name_en
 * @property string $name_ja
 *
 * @property AllocationBlock[] $allocationBlocks
 * @property KrfilterRegion[] $krfilterRegions
 * @property Krfilter[] $krfilters
 * @property MergedCidr[] $mergedCidrs
 * @property RegionStat[] $regionStats
 */
final class Region extends ActiveRecord
{
    use RegionNameTrait;

    public static function tableName(): string
    {
        return '{{%region}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id', 'name_ja', 'name_en'], 'required'],
            [['id'], 'string',
                'max' => 2,
            ],
            [['name_ja', 'name_en'], 'string',
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
            'name_en' => 'Name En',
            'name_ja' => 'Name Ja',
        ];
    }

    /**
     * @return ActiveQuery<AllocationBlock>
     */
    public function getAllocationBlocks(): ActiveQuery
    {
        return $this->hasMany(AllocationBlock::class, ['region_id' => 'id']);
    }

    /**
     * @return ActiveQuery<KrfilterRegion>
     */
    public function getKrfilterRegions(): ActiveQuery
    {
        return $this->hasMany(KrfilterRegion::class, ['region_id' => 'id']);
    }

    /**
     * @return ActiveQuery<Krfilter>
     */
    public function getKrfilters(): ActiveQuery
    {
        return $this->hasMany(Krfilter::class, ['id' => 'krfilter_id'])
            ->via('krfilterRegions');
    }

    /**
     * @return ActiveQuery<MergedCidr>
     */
    public function getMergedCidrs(): ActiveQuery
    {
        return $this->hasMany(MergedCidr::class, ['region_id' => 'id']);
    }

    /**
     * @return ActiveQuery<RegionStat>
     */
    public function getRegionStats(): ActiveQuery
    {
        return $this->hasMany(RegionStat::class, ['region_id' => 'id']);
    }
}
