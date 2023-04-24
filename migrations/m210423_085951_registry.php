<?php

declare(strict_types=1);

use app\helpers\TypeHelper;
use yii\db\Migration;

final class m210423_085951_registry extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%registry}}', [
            'id' => $this->string(7)->notNull()->check(vsprintf('[[id]] ~ %s', [
                TypeHelper::shouldBeDb($this->db)->quoteValue('^[a-z]+$'),
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

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%registry}}');

        return true;
    }
}
