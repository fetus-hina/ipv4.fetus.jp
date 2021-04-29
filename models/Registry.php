<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%registry}}".
 *
 * @property string $id
 * @property string|null $name
 *
 * @property AllocationBlock[] $allocationBlocks
 */
final class Registry extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%registry}}';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'string',
                'max' => 7,
            ],
            [['name'], 'string',
                'max' => 255,
            ],
            [['name'], 'unique'],
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

    public function getAllocationBlocks(): ActiveQuery
    {
        return $this->hasMany(AllocationBlock::class, ['registry_id' => 'id']);
    }
}
