<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '<cc:[a-z]{2}>.<template:[a-z0-9-]+>.txt' => 'region/plain',
        '<cc:[a-z]{2}>.txt' => 'region/plain',
        '<cc:[a-z]{2}>' => 'region/view',
        'about' => 'site/about',
        'index.json' => 'api/index-json',
        'ipv4bycc-cidr.txt' => 'ipv4bycc/cidr',
        'ipv4bycc-mask.txt' => 'ipv4bycc/mask',
        'krfilter' => 'krfilter/view',
        'krfilter.<id:[0-9]+>.<template:[a-z0-9-]+>.txt' => 'krfilter/plain',
        'krfilter.<id:[0-9]+>.txt' => 'krfilter/plain',
        'nginx-geo.txt' => 'nginx-geo/index',
        'robots.txt' => 'site/robots',
        'schema' => 'site/schema',
        'search' => 'search/index',
        'search/<query:[0-9.]+>' => 'search/compat',
        '' => 'site/index',
    ],
];
