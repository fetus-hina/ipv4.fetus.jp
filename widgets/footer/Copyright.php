<?php

declare(strict_types=1);

namespace app\widgets\footer;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\widgets\Icon;
use app\widgets\Tooltip;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function array_filter;
use function implode;
use function is_array;
use function is_string;
use function sprintf;
use function vsprintf;

final class Copyright extends Widget
{
    public function run(): string
    {
        return Html::tag(
            'div',
            implode(' ', array_filter(
                [
                    $this->renderCopyright(),
                    $this->renderTwitterLink(),
                    $this->renderGithubLink(),
                ],
                fn (?string $t): bool => $t !== null,
            )),
            [],
        );
    }

    private function renderCopyright(): string
    {
        return vsprintf('Copyright © %s %s.', [
            Html::encode($this->getCopyrightYear()),
            $this->getCopyrightHolder(),
        ]);
    }

    private function getCopyrightYear(): string
    {
        $value = ArrayHelper::getValue(Yii::$app->params, 'copyrightYear');
        return is_string($value) && $value !== ''
            ? $value
            : (new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone ?? 'Asia/Tokyo')))
                ->format('Y');
    }

    private function getCopyrightHolder(): string
    {
        $holder = ArrayHelper::getValue(Yii::$app->params, 'copyrightHolder');
        $website = ArrayHelper::getValue(Yii::$app->params, 'authorWebsite');

        return Html::a(
            Html::encode(
                is_string($holder) && $holder !== ''
                    ? $holder
                    : 'the Author',
            ),
            is_string($website) || is_array($website)
                ? $website
                : ['site/index'],
            [
                'rel' => 'external noopener',
                'target' => '_blank',
            ],
        );
    }

    private function renderTwitterLink(): ?string
    {
        $value = ArrayHelper::getValue(Yii::$app->params, 'authorTwitter');
        if (!is_string($value) || $value === '') {
            return null;
        }

        return Html::a(
            Tooltip::widget([
                'content' => Icon::twitter(),
                'format' => 'raw',
                'title' => sprintf('Twitter @%s', $value),
            ]),
            sprintf('https://twitter.com/%s', $value),
            [
                'target' => '_blank',
                'rel' => 'external noopener',
            ],
        );
    }

    private function renderGithubLink(): ?string
    {
        $value = ArrayHelper::getValue(Yii::$app->params, 'authorGitHub');
        if (!is_string($value) || $value === '') {
            return null;
        }

        return Html::a(
            Tooltip::widget([
                'content' => Icon::github(),
                'format' => 'raw',
                'title' => sprintf('GitHub @%s', $value),
            ]),
            sprintf('https://github.com/%s', $value),
            [
                'target' => '_blank',
                'rel' => 'external noopener',
            ],
        );
    }
}
