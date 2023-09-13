<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\commands\license\LicenseExtractTrait;
use yii\console\Controller;

final class LicenseController extends Controller
{
    use LicenseExtractTrait;

    /**
     * @var string
     */
    public $defaultAction = 'extract';
}
