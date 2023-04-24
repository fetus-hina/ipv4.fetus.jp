<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%newline}}".
 *
 * @property int $id
 * @property string $key
 * @property string $name
 *
 * @property DownloadTemplate[] $downloadTemplates
 */
final class Newline extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%newline}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id', 'key', 'name'], 'required'],
            [['id'], 'integer'],
            [['key', 'name'], 'string',
                'max' => 255,
            ],
            [['key'], 'unique'],
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
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getDownloadTemplates(): ActiveQuery
    {
        return $this->hasMany(DownloadTemplate::class, ['newline_id' => 'id']);
    }
}
