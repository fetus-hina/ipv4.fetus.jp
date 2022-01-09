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
  Yii::t('app', 'Database last updated: {updatedAt} ({took} sec.)', [
    'updatedAt' => Html::tag(
      'time',
      Html::encode(
        $timestamp
          ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
          ->format('Y-m-d H:i:s P')
      ),
      [
        'datetime' => $timestamp
          ->setTimezone(new DateTimeZone('Etc/UTC'))
          ->format(DateTime::ATOM),
      ]
    ),
    'took' => Yii::$app->formatter->asDecimal($params['dbUpdateTimestamp']['took'], 3),
  ]),
  ['class' => 'small'],
);
