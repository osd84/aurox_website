<?php

return [
    'appName' => 'OsdAurox',
    'appTitle' => 'OsdAurox',
    'appAuthor' => '-',
    'appDescription' => '-',
    'appKeywords' => '-',
    'appFavicon' => 'favicon.ico',
    'appLogo' => 'logo.png',
    'appLang' => 'fr',
    'appVersion' => '1.0.0',
    'devIp' => '127.0.0.1',

    'devUrl' => 'http://127.0.0.1:8000',
    'prodUrl' => 'https://127.0.0.1:8000',
    'appUrl' => 'https://127.0.0.1:8000',

    'debug' => false, // change me to false in production
    'host' => '127.0.0.1',
    'port' => '3306',
    'db' => 'aurox_tests',
    'user' => 'test',
    'pass' => '<changemeTestOnly£>',
    'charset' => 'utf8mb4',
    'passwordMinLength' => 8,
    'passwordMaxLength' => 255,
    'passwordComplexity' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
    'adminFolder' => 'admin',
    'nonce' => false,
    'salt' => '<ChangeTeSalt>',

    'mailHost' => '<changMe>',
    'mailPort' => 465,
    'mailTls' => False,
    'mailSsl' => True,
    'mailUser' => '<changMe>',
    'mailPass' => '<changMe>',
    'mailFrom' => '<changMe>',
    'mailContactDest' => '<changMe>',



    'lang' => ['fr', 'en', 'it'],
    'discordWebhook' => 'https://discord.com/api/webhooks/<change_me>',
];