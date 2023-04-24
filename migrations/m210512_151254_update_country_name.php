<?php

declare(strict_types=1);

use yii\db\Migration;

final class m210512_151254_update_country_name extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        return $this->doUpdate('new');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return $this->doUpdate('old');
    }

    private function doUpdate(string $key): bool
    {
        foreach ($this->getData() as $cc => $info) {
            $this->update(
                '{{%region}}',
                [
                    'name_ja' => $info[$key]['ja'],
                    'name_en' => $info[$key]['en'],
                ],
                [
                    'id' => $cc,
                ],
            );
        }

        return true;
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
    private function getData(): array
    {
        return [
            'sz' => [
                'old' => [
                    'ja' => 'スワジランド',
                    'en' => 'Swaziland',
                ],
                'new' => [
                    'ja' => 'エスワティニ',
                    'en' => 'Eswatini',
                ],
            ],
        ];
    }
}
