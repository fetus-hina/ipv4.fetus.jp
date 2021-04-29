<?php

declare(strict_types=1);

use yii\db\Migration;

final class m210424_235937_krfilter extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%krfilter}}', [
            'id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'PRIMARY KEY ([[id]])',
        ]);
        $this->batchInsert('{{%krfilter}}', ['id', 'name'], [
            [1, 'krfilter v1'],
            [2, 'krfilter v2'],
            [3, 'krfilter v3'],
            [4, 'eufilter v1'],
        ]);

        $this->createTable('{{%krfilter_region}}', [
            'krfilter_id' => $this->integer()->NotNull()->append('REFERENCES {{%krfilter}} ([[id]])'),
            'region_id' => $this->char(2)->notNull()->append('REFERENCES {{%region}} ([[id]])'),
            'PRIMARY KEY ([[krfilter_id]], [[region_id]])',
        ]);
        $this->batchInsert('{{%krfilter_region}}', ['krfilter_id', 'region_id'], [
            [1, 'cn'],
            [1, 'kp'],
            [1, 'kr'],

            [2, 'cn'],
            [2, 'hk'],
            [2, 'id'],
            [2, 'in'],
            [2, 'kp'],
            [2, 'kr'],
            [2, 'tw'],

            [3, 'cn'],
            [3, 'hk'],
            [3, 'id'],
            [3, 'in'],
            [3, 'kp'],
            [3, 'kr'],
            [3, 'ru'],
            [3, 'tw'],

            [4, 'at'],
            [4, 'ax'],
            [4, 'be'],
            [4, 'bg'],
            [4, 'cy'],
            [4, 'cz'],
            [4, 'de'],
            [4, 'dk'],
            [4, 'ee'],
            [4, 'es'],
            [4, 'eu'],
            [4, 'fi'],
            [4, 'fr'],
            [4, 'gb'],
            [4, 'gf'],
            [4, 'gi'],
            [4, 'gp'],
            [4, 'gr'],
            [4, 'hr'],
            [4, 'hu'],
            [4, 'ie'],
            [4, 'it'],
            [4, 'li'],
            [4, 'lt'],
            [4, 'lu'],
            [4, 'lv'],
            [4, 'mf'],
            [4, 'mq'],
            [4, 'mt'],
            [4, 'nl'],
            [4, 'no'],
            [4, 'pl'],
            [4, 'pt'],
            [4, 're'],
            [4, 'ro'],
            [4, 'se'],
            [4, 'si'],
            [4, 'sk'],
            // [4, 'uk'], // GB
            [4, 'yt'],
        ]);

        $this->createTable('{{%krfilter_cidr}}', [
            'id' => $this->bigPrimaryKey(),
            'krfilter_id' => $this->integer()->notNull()->append('REFERENCES {{%krfilter}} ([[id]])'),
            'cidr' => 'CIDR NOT NULL',
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->dropTable('{{%krfilter_cidr}}');
        $this->dropTable('{{%krfilter_region}}');
        $this->dropTable('{{%krfilter}}');

        return true;
    }
}
