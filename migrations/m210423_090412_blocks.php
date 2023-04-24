<?php

declare(strict_types=1);

use yii\db\Migration;

final class m210423_090412_blocks extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach ($this->getTables() as $tableName => $tableDef) {
            $this->createTable("{{%{$tableName}}}", $tableDef);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $tables = array_reverse(array_keys($this->getTables()));
        foreach ($tables as $tableName) {
            $this->dropTable("{{%{$tableName}}}");
        }
        return true;
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
    private function getTables(): array
    {
        return [
            'allocation_block' => [
                'id' => $this->bigPrimaryKey(),
                'start_address' => 'INET NOT NULL UNIQUE',
                'count' => $this->bigInteger()->notNull()->check('[[count]] > 0'),
                'registry_id' => $this->string(7)->notNull()->append('REFERENCES {{%registry}} ([[id]])'),
                'region_id' => $this->char(2)->notNull()->append('REFERENCES {{%region}} ([[id]])'),
                'date' => 'DATE NULL',
            ],
            'allocation_cidr' => [
                'id' => $this->bigPrimaryKey(),
                'block_id' => $this->bigInteger()->notNull()->append('REFERENCES {{%allocation_block}} ([[id]])'),
                'cidr' => 'CIDR NOT NULL UNIQUE',
            ],
            'merged_cidr' => [
                'id' => $this->bigPrimaryKey(),
                'region_id' => $this->char(2)->notNull()->append('REFERENCES {{%region}} ([[id]])'),
                'cidr' => 'CIDR NOT NULL UNIQUE',
            ],
            'region_stat' => [
                'region_id' => $this->char(2)->notNull()->append('REFERENCES {{%region}} ([[id]])'),
                'total_address_count' => $this->bigInteger()->notNull()->check('[[total_address_count]] >= 0'),
                'last_allocation_date' => 'DATE NULL',
            ],
        ];
    }
}
