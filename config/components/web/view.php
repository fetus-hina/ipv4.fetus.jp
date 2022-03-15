<?php

declare(strict_types=1);

use yii\base\View;
use yii\base\ViewEvent;

return (function (): array {
    $viewMethod = View::class . '::renderFile';

    return [
        'on ' . View::EVENT_BEFORE_RENDER => function (ViewEvent $event) use ($viewMethod): void {
            Yii::beginProfile(
                $event->viewFile,
                $viewMethod,
            );
        },
        'on ' . View::EVENT_AFTER_RENDER => function (ViewEvent $event) use ($viewMethod): void {
            Yii::endProfile(
                $event->viewFile,
                $viewMethod,
            );
        },
    ];
})();
