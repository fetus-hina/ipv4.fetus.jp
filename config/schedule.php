<?php

declare(strict_types=1);

/**
 * @var \omnilight\scheduling\Schedule $schedule
 */

$schedule
  ->command('update/index')
  ->dailyAt('6:05')
  ->withoutOverlapping();
