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
 * This is the model class for table "{{%comment_style}}".
 *
 * @property int $id
 * @property string|null $block_begin
 * @property string|null $block_end
 * @property string|null $line_begin
 * @property string|null $line_end
 * @property string $name
 *
 * @property DownloadTemplate[] $downloadTemplates
 */
final class CommentStyle extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%comment_style}}';
    }

    /**
     * @inheritdoc
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name', 'line_begin', 'line_end', 'block_begin', 'block_end'], 'string',
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
            'block_begin' => 'Block Begin',
            'block_end' => 'Block End',
            'line_begin' => 'Line Begin',
            'line_end' => 'Line End',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery<DownloadTemplate>
     */
    public function getDownloadTemplates(): ActiveQuery
    {
        return $this->hasMany(DownloadTemplate::class, ['comment_style_id' => 'id']);
    }
}
