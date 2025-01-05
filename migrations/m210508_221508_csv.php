<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\db\Migration;

final class m210508_221508_csv extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%download_template}}', [
            'key' => 'csv',
            'name' => 'CSV',
            'template' => implode(',', [
                '{cidr:csv}',
                '{network:csv}',
                '{broadcast:csv}',
                '{prefix:csv}',
                '{subnet:csv}',
            ]),
            'allow' => null,
            'deny' => null,
            'comment_style_id' => 1, // text
            'file_begin' => null,
            'file_end' => null,
            'list_begin' => '# ' . implode(',', [
                'CIDR',
                'network(start) address',
                'end address',
                'prefix',
                'subnet mask',
            ]),
            'list_end' => null,
            'usage' => null,
            'can_use_in_url' => true,
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%download_template}}', ['key' => 'csv']);

        return true;
    }
}
