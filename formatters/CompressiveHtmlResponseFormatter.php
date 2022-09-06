<?php

declare(strict_types=1);

namespace app\formatters;

use Yii;
use yii\web\HtmlResponseFormatter;

final class CompressiveHtmlResponseFormatter extends HtmlResponseFormatter
{
    /**
     * @inheritdoc
     * @return void
     */
    public function format($response)
    {
        if (
            \is_string($response->data) &&
            $this->isGzipAcceptable()
        ) {
            if ($compressed = \gzencode($response->data, 9)) {
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->add('Vary', 'Accept-Encoding');
                $response->content = $compressed;
                $response->data = null;
            }
        }

        parent::format($response);
    }

    private function isGzipAcceptable(): bool
    {
        return \in_array('gzip', $this->getAcceptEncodings(), true);
    }

    private function getAcceptEncodings(): array
    {
        $request = Yii::$app->request;
        return \array_keys(
            $request->parseAcceptHeader(
                $request->getHeaders()->get('Accept-Encoding')
            )
        );
    }
}
