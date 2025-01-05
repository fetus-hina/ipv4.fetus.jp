<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\db\Migration;

final class m210509_081223_update_country_name extends Migration
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
            'mk' => [
                'old' => [
                    'ja' => 'マケドニア旧ユーゴスラビア共和国',
                    'en' => 'Macedonia, the former Yugoslav Republic of',
                ],
                'new' => [
                    'ja' => '北マケドニア共和国',
                    'en' => 'North Macedonia, Republic of',
                ],
            ],
            'ps' => [
                'old' => [
                    'ja' => 'パレスチナ',
                    'en' => 'Palestinian Territory, Occupied',
                ],
                'new' => [
                    'ja' => 'パレスチナ',
                    'en' => 'Palestine',
                ],
            ],
            'tz' => [
                'old' => [
                    'ja' => 'タンザニア',
                    'en' => 'Tanzania, United Republic of',
                ],
                'new' => [
                    'ja' => 'タンザニア連合共和国',
                    'en' => 'Tanzania, United Republic of',
                ],
            ],
        ];
    }
}
