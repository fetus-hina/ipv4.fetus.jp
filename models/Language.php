<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%language}}".
 *
 * @property string $id
 * @property int $character_id
 * @property string $english_name
 * @property string|null $hreflang
 * @property bool $is_default
 * @property string $native_name
 * @property int $sort
 *
 * @property ?CharacterCategory $character
 * @property LanguageMatch[] $languageMatches
 */
final class Language extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%language}}';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id', 'character_id', 'native_name', 'english_name', 'sort'], 'required'],
            [['character_id', 'sort'], 'integer'],
            [['native_name', 'english_name'], 'string'],
            [['is_default'], 'boolean'],
            [['id'], 'string',
                'max' => 5,
            ],
            [['hreflang'], 'string',
                'max' => 2,
            ],
            [['hreflang'], 'unique'],
            [['sort'], 'unique'],
            [['id'], 'unique'],
            [['character_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => CharacterCategory::class,
                'targetAttribute' => [
                    'character_id' => 'id',
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
            'character_id' => 'Character ID',
            'english_name' => 'English Name',
            'hreflang' => 'Hreflang',
            'is_default' => 'Is Default',
            'native_name' => 'Native Name',
            'sort' => 'Sort',
        ];
    }

    public function getCharacter(): ActiveQuery
    {
        return $this->hasOne(CharacterCategory::class, ['id' => 'character_id']);
    }

    public function getLanguageMatches(): ActiveQuery
    {
        return $this->hasMany(LanguageMatch::class, ['language_id' => 'id']);
    }
}
