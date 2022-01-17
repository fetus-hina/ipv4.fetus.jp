<?php

declare(strict_types=1);

namespace app\controllers;

use app\actions\api\AllocationSummaryAction;
use yii\web\Controller;

final class ApiController extends Controller
{
    public function actions()
    {
        return [
            'allocation-summary' => AllocationSummaryAction::class,
        ];
    }
}
