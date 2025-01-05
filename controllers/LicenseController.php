<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\actions\license\LicenseAction;
use yii\web\Controller;

final class LicenseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $license = fn (string $title, string $dir, array $pageUrl): array => [
            'class' => LicenseAction::class,
            'directory' => $dir,
            'pageUrl' => $pageUrl,
            'title' => $title,
        ];

        return [
            'composer' => $license(
                Yii::t('app/license', 'Composer Packages'),
                '@app/data/licenses/composer',
                ['license/composer'],
            ),
            'font' => $license(
                Yii::t('app/license', 'Fonts'),
                '@app/data/licenses/font',
                ['license/font'],
            ),
            'npm' => $license(
                Yii::t('app/license', 'NPM Packages'),
                '@app/data/licenses/npm',
                ['license/npm'],
            ),
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
