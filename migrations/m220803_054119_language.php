<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\helpers\TypeHelper;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m220803_054119_language extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%character_category}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string()
                ->notNull()
                ->unique()
                ->append("CHECK ([[key]] ~ '^[a-z]+$')"),
            'name' => $this->string()->notNull(),
        ]);

        $this->createTable('{{%language}}', [
            'id' => $this->languageCodeColumn('id'),
            'hreflang' => $this->char(2)->null()->unique()->append("CHECK ([[hreflang]] ~ '^[a-z]{2}$')"),
            'character_id' => $this->integer()
                ->notNull()
                ->append('REFERENCES {{%character_category}}([[id]])'),
            'native_name' => 'TEXT NOT NULL',
            'english_name' => 'TEXT NOT NULL',
            'sort' => $this->integer()->notNull()->unique(),
            'is_default' => $this->boolean()->notNull()->defaultValue('f'),
            'PRIMARY KEY ([[id]])',
        ]);

        $this->createTable('{{%language_match}}', [
            'id' => $this->languageCodeColumn('id', ambiguous: true),
            'language_id' => $this->char(strlen('ja-JP'))
                ->notNull()
                ->append('REFERENCES {{%language}} ([[id]])'),
            'priority' => $this->integer()->notNull()->unique(),
            'PRIMARY KEY ([[id]])',
        ]);

        $this->batchInsert(
            '{{%character_category}}',
            ['key', 'name'],
            [
                ['latin', 'Latin'],
                ['japanese', 'Japanese'],
            ],
        );

        $this->batchInsert(
            '{{%language}}',
            ['id', 'hreflang', 'character_id', 'native_name', 'english_name', 'sort', 'is_default'],
            [
                ['en-US', 'en', $this->getCharacterCategoryId('latin'), 'English', 'English', 10, true],
                ['ja-JP', 'ja', $this->getCharacterCategoryId('japanese'), '日本語', 'Japanese', 20, false],
            ],
        );

        $this->batchInsert(
            '{{%language_match}}',
            ['id', 'language_id', 'priority'],
            [
                ['ja-*', 'ja-JP', 1000],
                ['en-*', 'en-US', 2000],
                ['ja', 'ja-JP', 1999],
                ['en', 'en-US', 2999],
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        foreach (['{{%language_match}}', '{{%language}}', '{{%character_category}}'] as $t) {
            $this->dropTable($t);
        }

        return true;
    }

    private function languageCodeColumn(string $columnName, bool $ambiguous = false): string
    {
        $db = TypeHelper::shouldBeDb($this->db);
        $asterisk = preg_quote('*');

        return vsprintf('%s(%d) NOT NULL UNIQUE CHECK (%s ~ %s)', [
            $ambiguous ? 'VARCHAR' : 'CHAR',
            strlen('ja-JP'),
            $db->quoteColumnName($columnName),
            $db->quoteValue(
                $ambiguous
                    ? "^[a-z]{2}(-([A-Z]{2}|{$asterisk}))?"
                    : '^[a-z]{2}-[A-Z]{2}$',
            ),
        ]);
    }

    private function getCharacterCategoryId(string $key): int
    {
        $cache = null;
        if ($cache === null) {
            $cache = ArrayHelper::map(
                (new Query())
                    ->select(['id', 'key'])
                    ->from('{{%character_category}}')
                    ->all(TypeHelper::shouldBeDb($this->db)),
                'key',
                'id',
            );
        }

        return $cache[$key] ?? throw new Exception();
    }
}
