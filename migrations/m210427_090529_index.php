<?php

declare(strict_types=1);

use yii\db\Migration;

class m210427_090529_index extends Migration
{
    public function safeUp()
    {
        $this->createIndex('allocation_block__region_id__idx', '{{%allocation_block}}', ['region_id'], false);
        $this->createIndex('allocation_cidr__block_id__idx', '{{%allocation_cidr}}', ['block_id'], false);
        $this->createIndex('merged_cidr__region_id__idx', '{{%merged_cidr}}', ['region_id', 'cidr'], true);
        $this->createIndex('krfilter_cidr__krfilter_id__idx', '{{%krfilter_cidr}}', ['krfilter_id', 'cidr'], true);

        $this->execute(
            'CREATE INDEX [[allocation_cidr__cidr__idx]] ON {{%allocation_cidr}} USING gist ([[cidr]] inet_ops)'
        );
        $this->execute(
            'CREATE INDEX [[merged_cidr__cidr__idx]] ON {{%merged_cidr}} USING gist ([[cidr]] inet_ops)'
        );
        return true;
    }

    public function safeDown()
    {
        $this->dropIndex('merged_cidr__cidr__idx', '{{%merged_cidr}}');
        $this->dropIndex('allocation_cidr__cidr__idx', '{{%allocation_cidr}}');
        $this->dropIndex('krfilter_cidr__krfilter_id__idx', '{{%krfilter_cidr}}');
        $this->dropIndex('allocation_cidr__block_id__idx', '{{%allocation_cidr}}');
        $this->dropIndex('allocation_block__region_id__idx', '{{%allocation_block}}');
        $this->dropIndex('merged_cidr__region_id__idx', '{{%merged_cidr}}');
        return true;
    }
}
