<?php

declare(strict_types=1);

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '<cc:[a-z]{2}>.<template:[a-z0-9-]+>.txt' => 'region/plain',
        '<cc:[a-z]{2}>.txt' => 'region/plain',
        '<cc:[a-z]{2}>' => 'region/view',
        'krfilter' => 'krfilter/view',
        'krfilter.<id:[0-9]+>.<template:[a-z0-9-]+>.txt' => 'krfilter/plain',
        'krfilter.<id:[0-9]+>.txt' => 'krfilter/plain',
        'search' => 'search/index',
        'robots.txt' => 'site/robots',
        '' => 'site/index',
    ],
];
