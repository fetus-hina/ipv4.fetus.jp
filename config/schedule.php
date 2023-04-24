<?php // phpcs:disable SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration.MissingVariable

declare(strict_types=1);

use omnilight\scheduling\Schedule;

/**
 * @var Schedule $schedule
 */

$schedule
  ->command('update/index')
  ->dailyAt('6:05')
  ->withoutOverlapping();
