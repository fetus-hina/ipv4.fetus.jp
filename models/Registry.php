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
     * @inheritdoc
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
