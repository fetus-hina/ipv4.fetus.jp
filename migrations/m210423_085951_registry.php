<?php

declare(strict_types=1);

use yii\db\Migration;

final class m210423_085951_registry extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%registry}}', [
            'id' => $this->string(7)->notNull()->check(vsprintf('[[id]] ~ %s', [
                $this->db->quoteValue('^[a-z]+$'),
            ])),
            'name' => $this->string()->unique(),
            'PRIMARY KEY ([[id]])',
        ]);

        $this->batchInsert('{{%registry}}', ['id', 'name'], [
            ['afrinic', 'AfriNIC'],
            ['apnic', 'APNIC'],
            ['arin', 'ARIN'],
            ['iana', 'IANA'],
            ['lacnic', 'LACNIC'],
            ['ripencc', 'RIPE NCC'],
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->dropTable('{{%registry}}');

        return true;
    }
}
