<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%allocation_cidr}}".
 *
 * @property int $id
 * @property int $block_id
 * @property string $cidr
 *
 * @property ?AllocationBlock $block
 */
final class AllocationCidr extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%allocation_cidr}}';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['block_id', 'cidr'], 'required'],
            [['block_id'], 'integer'],
            [['cidr'], 'string'],
            [['cidr'], 'unique'],
            [['block_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => AllocationBlock::class,
                'targetAttribute' => [
                    'block_id' => 'id',
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
            'block_id' => 'Block ID',
            'cidr' => 'Cidr',
        ];
    }

    public function getBlock(): ActiveQuery
    {
        return $this->hasOne(AllocationBlock::class, ['id' => 'block_id']);
    }
}
