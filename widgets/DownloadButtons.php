<?php

declare(strict_types=1);

namespace app\widgets;

use LogicException;
use RuntimeException;
use Yii;
use app\models\DownloadTemplate;
use yii\base\Widget;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\web\View;

use function array_filter;
use function array_map;
use function call_user_func;
use function implode;
use function is_array;
use function sprintf;
use function substr;

use const SORT_ASC;

final class DownloadButtons extends Widget
{
    /**
     * @var callable(?DownloadTemplate $template): array|null
     */
    public $downloadLinkCreator = null;

    public bool $enableIpv4bycc = true;

    public function run(): string
    {
        return Html::tag(
            'nav',
            implode('', [
                $this->renderPlainText(),
                $this->renderTemplates(),
            ]),
        );
    }

    private function renderPlainText(): string
    {
        return Html::tag(
            'div',
            Html::a(
                Yii::t('app', 'Plain Text'),
                $this->callLinkCreator(null),
                [
                    'class' => [
                        'btn',
                        'btn-primary',
                    ],
                    'type' => 'text/plain',
                ],
            ),
            [
                'class' => [
                    'mb-2',
                    'd-grid',
                ],
            ],
        );
    }

    private function renderTemplates(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderTemplateButton(),
                $this->renderTemplateDropdown(),
            ]),
            [
                'class' => [
                    'mb-0',
                    'd-grid',
                ],
            ],
        );
    }

    private function renderTemplateButton(): string
    {
        if (($view = $this->view) instanceof View) {
            BootstrapPluginAsset::register($view);
        }

        return Html::button(
            Yii::t('app', 'Access-Control Templates'),
            [
                'aria' => [
                    'expanded' => 'false',
                    'haspopup' => 'true',
                ],
                'class' => [
                    'btn',
                    'btn-primary',
                    'dropdown-toggle',
                ],
                'data' => [
                    'bs-toggle' => 'dropdown',
                ],
                'id' => $this->getTemplateButtonId(),
                'type' => 'button',
            ],
        );
    }

    private function renderTemplateDropdown(): string
    {
        return Html::tag(
            'div',
            implode('', array_map(
                fn (DownloadTemplate $model): string => Html::a(
                    Html::encode($model->name),
                    $this->callLinkCreator($model),
                    [
                        'class' => [
                            'dropdown-item',
                        ],
                        'type' => 'text/plain',
                    ],
                ),
                array_filter(
                    $this->getAllTemplates(),
                    fn (DownloadTemplate $model): bool => $this->enableIpv4bycc ||
                        substr($model->key, 0, 9) !== 'ipv4bycc-',
                ),
            )),
            [
                'aria' => [
                    'labelledby' => $this->getTemplateButtonId(),
                ],
                'class' => [
                    'dropdown-menu',
                    'shadow',
                ],
            ],
        );
    }

    /**
     * @var DownloadTemplate[]
     */
    private static array $templates = [];

    /**
     * @return DownloadTemplate[]
     */
    private function getAllTemplates(): array
    {
        if (!self::$templates) {
            self::$templates = DownloadTemplate::find()
                ->andWhere(['can_use_in_url' => true])
                ->orderBy(['key' => SORT_ASC])
                ->all();
        }

        return self::$templates;
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
    private function callLinkCreator(?DownloadTemplate $template): array
    {
        if (($callable = $this->downloadLinkCreator) === null) {
            throw new LogicException();
        }

        $value = call_user_func($callable, $template);
        return is_array($value)
            ? $value
            : throw new RuntimeException();
    }

    private function getTemplateButtonId(): string
    {
        return sprintf('%s-btn-template', (string)$this->id);
    }
}
