<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%character_category}}".
 *
 * @property int $id
 * @property string $key
 * @property string $name
 *
 * @property Language[] $languages
 */
final class CharacterCategory extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%character_category}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string',
                'max' => 255,
            ],
            [['key'], 'unique'],
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
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery<Language>
     */
    public function getLanguages(): ActiveQuery
    {
        return $this->hasMany(Language::class, ['character_id' => 'id']);
    }
}
