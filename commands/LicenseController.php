<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
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
