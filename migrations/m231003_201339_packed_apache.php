<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\db\Migration;

final class m231003_201339_packed_apache extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%download_template}}',
            'line_limit',
            (string)$this->integer()->null(),
        );

        $this->insert(
            '{{%download_template}}',
            [
                'key' => 'apache22-packed',
                'name' => 'Apache (.htaccess), packed',
                'template' => '{control} from {cidr_list}',
                'allow' => 'allow',
                'deny' => 'deny',
                'comment_style_id' => 1, // shell style
                'can_use_in_url' => true,
                'newline_id' => 1, // LF
                'line_limit' => 1000, // 1024バイトまでらしい?
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%download_template}}', ['key' => 'apache22-packed']);
        $this->dropColumn('{{%download_template}}', 'line_limit');

        return true;
    }
}
