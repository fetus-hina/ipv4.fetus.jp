<?php

declare(strict_types=1);

use app\helpers\TypeHelper;
use app\models\CommentStyle;
use app\models\DownloadTemplate;
use yii\db\Migration;
use yii\db\Query;

final class m220523_051548_ipv4bycc extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $commentStyleId = filter_var(
            (new Query())
                ->select(['id'])
                ->from(CommentStyle::tableName())
                ->andWhere(['name' => 'shell'])
                ->scalar(TypeHelper::shouldBeDb($this->db)),
            FILTER_VALIDATE_INT,
        );
        if (!is_int($commentStyleId)) {
            return false;
        }

        $this->batchInsert(
            DownloadTemplate::tableName(),
            ['key', 'name', 'template', 'comment_style_id', 'can_use_in_url'],
            [
                [
                    'ipv4bycc-cidr',
                    'ipv4bycc compat., CIDR',
                    "{cc:upper}\t{cidr}",
                    $commentStyleId,
                    true,
                ],
                [
                    'ipv4bycc-mask',
                    'ipv4bycc compat., Mask',
                    "{cc:upper}\t{network}/{subnet}",
                    $commentStyleId,
                    true,
                ],
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            DownloadTemplate::tableName(),
            [
                'key' => ['ipv4bycc-cidr', 'ipv4bycc-mask'],
            ],
        );

        return true;
    }
}
