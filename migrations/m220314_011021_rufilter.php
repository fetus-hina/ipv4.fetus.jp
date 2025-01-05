<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\db\Migration;

final class m220314_011021_rufilter extends Migration
{
    private const FILTER_ID = 5;
    private const FILTER_NAME = 'rufilter v1';

    private const CC_BELARUS = 'by';
    private const CC_RUSSIA = 'ru';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert(
            '{{%krfilter}}',
            [
                'id' => self::FILTER_ID,
                'name' => self::FILTER_NAME,
            ],
        );
        $this->batchInsert(
            '{{%krfilter_region}}',
            [
                'krfilter_id',
                'region_id',
            ],
            [
                [self::FILTER_ID, self::CC_BELARUS],
                [self::FILTER_ID, self::CC_RUSSIA],
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            '{{%krfilter_region}}',
            [
                'krfilter_id' => self::FILTER_ID,
            ],
        );

        $this->delete(
            '{{%krfilter}}',
            [
                'id' => self::FILTER_ID,
            ],
        );

        return true;
    }
}
