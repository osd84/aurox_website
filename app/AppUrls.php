<?php

namespace App;

use OsdAurox\Sec;

class AppUrls
{
    public const HOME = '/';

    # 404
    public const NOT_FOUND = '/404.php';

    public const PAGE_CONTACT = '/contact.php';
    public const PAGE_AJAX = '/ajax.php';
    public const RSS = '/rss.php';

    public const PAGE_FORMS = '/forms.php';
    public const PAGE_MODALS = '/modals.php';
    public const PAGE_MOBILE = '/mobile.php';
    public const CATEGORY_SELECT2 = '/category_select2.php';

    // CRON

    // API
    public const AJAX_ROULETTE = '/ajax/roulette.php';


    public static function getList(): array
    {
        $reflect = new \ReflectionClass(__CLASS__);
        return $reflect->getConstants();
    }

    public static function existOr404()
    {
        if(!isset($_SERVER['REQUEST_URI'])) {
            return true;
        }
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        $url = trim($url, '/');
        $url = explode('?', $url);
        $url = $url[0];
        $url = trim($url, '/');
        $url = strtolower($url);
        if(str_starts_with('/', $url)){
            $url = substr($url, 1);
        }
        $url = '/' . $url;
        if (in_array($url, ['/', '/index.php']))
        {
            return true;
        }
        $urls = self::getList();
        if(in_array($url, $urls)) {
            return true;
        }
        header('Location: ' . AppUrls::NOT_FOUND . '?url=' . Sec::hNoHtml($url));
        exit;
    }
}