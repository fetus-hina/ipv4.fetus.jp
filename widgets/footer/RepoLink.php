<?php

declare(strict_types=1);

namespace app\widgets\footer;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

final class RepoLink extends Widget
{
    public function run(): string
    {
        $repoUrl = ArrayHelper::getValue(Yii::$app->params, 'repository');
        if (!\is_string($repoUrl) && !\is_array($repoUrl)) {
            return '';
        }

        $gitInfo = ArrayHelper::getValue(Yii::$app->params, 'gitRevision');
        if (
            !\is_array($gitInfo) ||
            !isset($gitInfo['hash']) ||
            !isset($gitInfo['short']) ||
            !isset($gitInfo['version']) ||
            !\is_string($gitInfo['hash']) ||
            !\is_string($gitInfo['short']) ||
            !\is_string($gitInfo['version'])
        ) {
            $gitInfo = null;
        }

        return $this->renderMain($repoUrl, $gitInfo);
    }

    private function renderMain(string|array $repoUrl, ?array $gitInfo): string
    {
        return Html::tag(
            'div',
            \implode(', ', \array_map(
                fn (string $html): string => Html::tag(
                    'span',
                    $html,
                    [
                        'class' => [
                            'text-nowrap',
                        ],
                    ],
                ),
                \array_filter(
                    [
                        $this->renderRepoLink($repoUrl),
                        $this->renderVersion($gitInfo),
                        $this->renderRevision($gitInfo),
                    ],
                    fn (?string $v): bool => $v !== null,
                ),
            )),
            [
                'class' => [
                    'small',
                ],
            ],
        );
    }

    private function renderRepoLink(string|array $repoUrl): string
    {
        return Html::a(
            Html::encode(Yii::t('app', 'Source Code')),
            $repoUrl,
            [
                'target' => '_blank',
                'rel' => 'external noopener noreferrer',
            ],
        );
    }

    private function renderVersion(?array $info): ?string
    {
        return $info && $info['version'] !== ''
            ? Html::encode($info['version'])
            : null;
    }

    private function renderRevision(?array $info): ?string
    {
        return $info && $info['short'] !== ''
            ? Html::encode($info['short'])
            : null;
    }
}
