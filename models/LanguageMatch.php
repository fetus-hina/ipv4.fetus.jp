<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%language_match}}".
 *
 * @property string $id
 * @property string $language_id
 * @property int $priority
 *
 * @property ?Language $language
 */
final class LanguageMatch extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%language_match}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id', 'language_id', 'priority'], 'required'],
            [['priority'], 'integer'],
            [['id', 'language_id'], 'string',
                'max' => 5,
            ],
            [['priority'], 'unique'],
            [['id'], 'unique'],
            [['language_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => [
                    'language_id' => 'id',
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
            'language_id' => 'Language ID',
            'priority' => 'Priority',
        ];
    }

    public function getLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['id' => 'language_id']);
    }
}
