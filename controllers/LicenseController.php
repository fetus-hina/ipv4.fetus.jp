<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\actions\license\LicenseAction;
use yii\web\Controller;

final class LicenseController extends Controller
{
    public function actions()
    {
        $license = fn (string $title, string $dir): array => [
            'class' => LicenseAction::class,
            'directory' => $dir,
            'title' => $title,
        ];

        return [
            'composer' => $license(
                Yii::t('app/license', 'Composer Packages'),
                '@app/data/licenses/composer',
            ),
            'npm' => $license(
                Yii::t('app/license', 'NPM Packages'),
                '@app/data/licenses/npm',
            ),
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
