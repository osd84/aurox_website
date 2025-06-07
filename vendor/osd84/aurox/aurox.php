<?php

// Const for all APP
if(!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(__DIR__));
}
// SEC - unique request id used by scp
define('REQUEST_ID', bin2hex(random_bytes(16)));
define('NONCE', base64_encode(random_bytes(16)));

require_once __DIR__ . '/vendor/autoload.php';


use App\AppUrls;
use OsdAurox\AppConfig;
use OsdAurox\Ban;
use OsdAurox\Dbo;
use OsdAurox\ErrorMonitoring;
use OsdAurox\I18n;
use OsdAurox\Log;
use OsdAurox\Sec;

// AppConfig
AppConfig::init(require APP_ROOT . '/conf.php');


// ERRORS
if (AppConfig::get('debug', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    Log::getInstance(path : '/logs/dev.log');
    DEFINE('DEBUG', true);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);

    Log::getInstance(path : '/logs/prod.log');
    DEFINE('DEBUG', false);

    // On initalize le monitoring des erreurs fatales
    ErrorMonitoring::initialize();
}

// SESSION
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start(); //by default requires session storage


if(AppConfig::get('nonce')) {
    header("Content-Security-Policy: script-src 'self' 'nonce-". Sec::noneCsp() ."'; object-src 'none';");
}
if (!AppConfig::get('debug')) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        $appUrl = AppConfig::get('appUrl');
        if (!str_contains($appUrl, 'https://')) {
            die('HTTPS is required in PROD');
        }
        header('Location: ' . AppConfig::get('appUrl'));
        exit;
    }

    // SI c'est https on active les cokies sécurisés
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        session_set_cookie_params([
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
}

// DB
Dbo::getInstance(
    AppConfig::get('host'),
    AppConfig::get('port'),
    AppConfig::get('db'),
    AppConfig::get('user'),
    AppConfig::get('pass', safe: true),
    AppConfig::get('charset')
);

// Flash
$GLOBALS['messages'] = [];

// ban system
Ban::blockBlackListed();
Ban::checkRequest();

// i18n
$GLOBALS['i18n'] = new I18n();
$locale = $_SESSION['locale'] ?? 'fr';
$GLOBALS['i18n']->setLocale($locale);


// On stocke le referer dans la session
Sec::storeReferer();

AppUrls::existOr404();