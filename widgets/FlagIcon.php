<?php

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use Yii;
use app\assets\FlagIconsAsset;
use app\assets\InlineFlagIconsAsset;
use app\helpers\Unicode;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

use function base64_encode;
use function file_exists;
use function file_get_contents;
use function preg_match;
use function rawurlencode;
use function strlen;
use function strtolower;
use function trim;
use function vsprintf;

final class FlagIcon extends Widget
{
    public string $cc = 'xx';
    public bool $useImg = false;

    public function run(): string
    {
        $cc = trim(strtolower($this->cc));

        if (!preg_match('/^[a-zA-Z]{2}$/', $cc)) {
            throw new LogicException("Invalid CC: {$cc}");
        }

        if ($this->useImg) {
            if ($content = $this->renderImgTag($cc)) {
                return $content;
            }
        }

        if (($view = $this->view) instanceof View) {
            FlagIconsAsset::register($view);
        }

        return Html::tag(
            'span',
            $this->renderCss($cc),
            [
                'class' => [
                    'fi',
                    "fi-{$cc}",
                ],
            ],
        );
    }

    private function renderCss(string $cc): string
    {
        return Html::tag(
            'span',
            Unicode::asciiToRegionalIndicator($cc),
            [
                'class' => [
                    'visually-hidden',
                ],
            ],
        );
    }

    private function renderImgTag(string $cc): ?string
    {
        $svgPath = Yii::getAlias("@vendor/lipis/flag-icons/flags/4x3/{$cc}.svg");
        if (
            !$svgPath ||
            !is_string($svgPath) ||
            !@file_exists($svgPath) ||
            !($dataUri = $this->createDataUri($svgPath))
        ) {
            return null;
        }

        if (($view = $this->view) instanceof View) {
            InlineFlagIconsAsset::register($view);
        }

        return Html::img($dataUri, [
            'alt' => Unicode::asciiToRegionalIndicator($cc),
            'class' => 'inline-flag-icons',
        ]);
    }

    private function createDataUri(string $svgPath): ?string
    {
        if (!$content = @file_get_contents($svgPath)) {
            return null;
        }

        $b64 = base64_encode($content);
        $hex = rawurlencode($content);
        $useB64 = strlen($b64) < strlen($hex);

        return vsprintf('data:image/svg+xml%s,%s', [
            $useB64 ? ';base64' : '',
            $useB64 ? $b64 : $hex,
        ]);
    }
}
