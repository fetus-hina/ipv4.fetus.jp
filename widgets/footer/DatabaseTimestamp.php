<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\widgets\footer;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function is_array;
use function is_float;

final class DatabaseTimestamp extends Widget
{
    public function run(): string
    {
        $info = ArrayHelper::getValue(Yii::$app->params, 'dbUpdateTimestamp');
        if (
            !is_array($info) ||
            !isset($info['startAt']) ||
            !isset($info['finishAt']) ||
            !isset($info['took']) ||
            !$info['startAt'] instanceof DateTimeInterface ||
            !$info['finishAt'] instanceof DateTimeInterface ||
            !is_float($info['took'])
        ) {
            return '';
        }

        return $this->renderContent(
            DateTimeImmutable::createFromInterface($info['startAt']),
            DateTimeImmutable::createFromInterface($info['finishAt']),
            $info['took'],
        );
    }

    private function renderContent(
        DateTimeImmutable $startAt,
        DateTimeImmutable $finishAt,
        float $tookSec,
    ): string {
        return Html::tag(
            'div',
            Yii::t(
                'app',
                'Database last updated: {updatedAt} ({took} sec.)',
                [
                    'updatedAt' => $this->renderTimestamp($finishAt),
                    'took' => Yii::$app->formatter->asDecimal($tookSec, 3),
                ],
            ),
            [
                'class' => [
                    'small',
                ],
            ],
        );
    }

    private function renderTimestamp(DateTimeImmutable $timestamp): string
    {
        return Html::tag(
            'time',
            Html::encode(
                $timestamp
                    ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
                    ->format('Y-m-d H:i:s P'),
            ),
            [
                'datetime' => $timestamp
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->format(DateTimeInterface::ATOM),
            ],
        );
    }
}
