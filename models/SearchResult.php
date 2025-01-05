<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\base\BaseObject;

class SearchResult extends BaseObject
{
    public Region $region;
    public AllocationBlock $block;
    public AllocationCidr $cidr;
    public MergedCidr $mergedCidr;
}
