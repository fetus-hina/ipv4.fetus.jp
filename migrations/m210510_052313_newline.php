<?php

declare(strict_types=1);

use yii\db\Migration;

final class m210510_052313_newline extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%newline}}', [
            'id' => $this->integer()->notNull(),
            'key' => $this->string()->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'PRIMARY KEY ([[id]])',
        ]);
        $this->batchInsert('{{%newline}}', ['id', 'key', 'name'], [
            [1, 'unix', 'LF'],
            [2, 'win', 'CR+LF'],
        ]);

        $this->addColumn(
            '{{%download_template}}',
            'newline_id',
            (string)$this->integer()->null()->append('REFERENCES {{%newline}}([[id]])')
        );

        $this->update(
            '{{%download_template}}',
            ['newline_id' => 2],
            ['key' => 'csv']
        );

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('{{%download_template}}', 'newline_id');
        $this->dropTable('{{%newline}}');

        return true;
    }
}
