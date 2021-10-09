<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%krfilter_cidr}}".
 *
 * @property int $id
 * @property string $cidr
 * @property int $krfilter_id
 *
 * @property ?Krfilter $krfilter
 */
final class KrfilterCidr extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%krfilter_cidr}}';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['krfilter_id', 'cidr'], 'required'],
            [['krfilter_id'], 'integer'],
            [['cidr'], 'string'],
            [['krfilter_id', 'cidr'], 'unique',
                'skipOnEmpty' => true,
                'skipOnError' => true,
                'targetAttribute' => [
                    'krfilter_id',
                    'cidr',
                ],
            ],
            [['krfilter_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Krfilter::class,
                'targetAttribute' => [
                    'krfilter_id' => 'id',
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
            'krfilter_id' => 'Krfilter ID',
        ];
    }

    public function getKrfilter(): ActiveQuery
    {
        return $this->hasOne(Krfilter::class, ['id' => 'krfilter_id']);
    }
}
