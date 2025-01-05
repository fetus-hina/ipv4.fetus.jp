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
 * This is the model class for table "{{%krfilter_region}}".
 *
 * @property int $krfilter_id
 * @property string $region_id
 *
 * @property ?Krfilter $krfilter
 * @property ?Region $region
 */
final class KrfilterRegion extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%krfilter_region}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['krfilter_id', 'region_id'], 'required'],
            [['krfilter_id'], 'integer'],
            [['region_id'], 'string',
                'max' => 2,
            ],
            [['krfilter_id', 'region_id'], 'unique',
                'skipOnEmpty' => true,
                'skipOnError' => true,
                'targetAttribute' => [
                    'krfilter_id',
                    'region_id',
                ],
            ],
            [['krfilter_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Krfilter::class,
                'targetAttribute' => [
                    'krfilter_id' => 'id',
                ],
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
            'krfilter_id' => 'Krfilter ID',
            'region_id' => 'Region ID',
        ];
    }

    public function getKrfilter(): ActiveQuery
    {
        return $this->hasOne(Krfilter::class, ['id' => 'krfilter_id']);
    }

    public function getRegion(): ActiveQuery
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }
}
