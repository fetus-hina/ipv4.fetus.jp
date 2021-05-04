<?php

declare(strict_types=1);

use yii\helpers\Html;

$params = Yii::$app->params;
if (
    !isset($params['dbUpdateTimestamp']) ||
    !is_array($params['dbUpdateTimestamp']) ||
    !isset($params['dbUpdateTimestamp']['startAt']) ||
    !isset($params['dbUpdateTimestamp']['finishAt']) ||
    !isset($params['dbUpdateTimestamp']['took'])
) {
  return;
}

$timestamp = $params['dbUpdateTimestamp']['finishAt'];
if (!($timestamp instanceof DateTimeImmutable)) {
  return;
}

echo Html::tag(
  'div',
  vsprintf('DB最終更新日時: %s (%.3f 秒)', [
    Html::tag(
      'time',
      Html::encode(
        $timestamp
          ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
          ->format('Y-m-d H:i:s')
      ),
      [
        'datetime' => $timestamp
          ->setTimezone(new DateTimeZone('Etc/UTC'))
          ->format(DateTime::ATOM),
      ]
    ),
    $params['dbUpdateTimestamp']['took'],
  ]),
  ['class' => 'small']
);
