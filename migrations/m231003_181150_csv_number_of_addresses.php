<?php

declare(strict_types=1);

use yii\db\Migration;

final class m231003_181150_csv_number_of_addresses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%download_template}}',
            [
                'template' => implode(',', [
                    '{cidr:csv}',
                    '{network:csv}',
                    '{broadcast:csv}',
                    '{prefix:csv}',
                    '{subnet:csv}',
                    '{count:csv}',
                ]),
                'list_begin' => '# ' . implode(',', [
                    'CIDR',
                    'network(start) address',
                    'end address',
                    'prefix',
                    'subnet mask',
                    'number of addresses',
                ]),
            ],
            ['key' => 'csv'],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update(
            '{{%download_template}}',
            [
                'template' => implode(',', [
                    '{cidr:csv}',
                    '{network:csv}',
                    '{broadcast:csv}',
                    '{prefix:csv}',
                    '{subnet:csv}',
                ]),
                'list_begin' => '# ' . implode(',', [
                    'CIDR',
                    'network(start) address',
                    'end address',
                    'prefix',
                    'subnet mask',
                ]),
            ],
            ['key' => 'csv'],
        );

        return true;
    }
}
