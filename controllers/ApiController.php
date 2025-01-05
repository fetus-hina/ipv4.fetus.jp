<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\api\AllocationSummaryAction;
use app\actions\api\IndexJsonAction;
use yii\web\Controller;

final class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'allocation-summary' => AllocationSummaryAction::class,
            'index-json' => IndexJsonAction::class,
        ];
    }
}
