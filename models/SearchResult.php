<?php

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
