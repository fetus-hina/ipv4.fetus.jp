<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%download_template}}".
 *
 * @property int $id
 * @property string|null $allow
 * @property bool $can_use_in_url
 * @property int $comment_style_id
 * @property string|null $deny
 * @property string|null $file_begin
 * @property string|null $file_end
 * @property string $key
 * @property string|null $list_begin
 * @property string|null $list_end
 * @property string $name
 * @property int|null $newline_id
 * @property string $template
 * @property string|null $usage
 *
 * @property ?CommentStyle $commentStyle
 * @property ?Newline $newline
 */
final class DownloadTemplate extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%download_template}}';
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['key', 'name', 'template', 'comment_style_id'], 'required'],
            [['comment_style_id', 'newline_id'], 'integer'],
            [['file_begin', 'file_end', 'list_begin', 'list_end', 'usage'], 'string'],
            [['can_use_in_url'], 'boolean'],
            [['key'], 'string',
                'max' => 16,
            ],
            [['name', 'template', 'allow', 'deny'], 'string',
                'max' => 255,
            ],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['comment_style_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => CommentStyle::class,
                'targetAttribute' => [
                    'comment_style_id' => 'id',
                ],
            ],
            [['newline_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Newline::class,
                'targetAttribute' => [
                    'newline_id' => 'id',
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
            'allow' => 'Allow',
            'can_use_in_url' => 'Can Use In Url',
            'comment_style_id' => 'Comment Style ID',
            'deny' => 'Deny',
            'file_begin' => 'File Begin',
            'file_end' => 'File End',
            'key' => 'Key',
            'list_begin' => 'List Begin',
            'list_end' => 'List End',
            'name' => 'Name',
            'newline_id' => 'Newline ID',
            'template' => 'Template',
            'usage' => 'Usage',
        ];
    }

    public function getCommentStyle(): ActiveQuery
    {
        return $this->hasOne(CommentStyle::class, ['id' => 'comment_style_id']);
    }

    public function getNewline(): ActiveQuery
    {
        return $this->hasOne(Newline::class, ['id' => 'newline_id']);
    }
}
