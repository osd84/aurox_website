<?php

namespace OsdAurox;


use OsdAurox\MobileDetect;

class Base
{

    /**
     * Determines if the current device is a mobile device or tablet.
     *
     * @return bool Returns true if the device is a mobile or tablet, otherwise false.
     */
    public static function isMobile(): bool
    {
        // mobile detected
        $detect = new MobileDetect();
        if ($detect->isMobile()) {
            return true;
        }
        if ($detect->isTablet()) {
            return true;
        }
        return false;
    }


    /**
     * Terminates the execution of the script or throws an exception depending on the context.
     *
     * @param string $message Optional message to display or include in the exception.
     * @return void
     */
    public static function dieOrThrow(string $message = ''): void
    {
        if(defined('UNIT_TESTING')) {
            throw new \RuntimeException('[STOPPED by dieOrThrow() ]' . $message);
        }
        die($message);
    }


    /**
     * Retourne filtre une list de dict pour ne conserver que id, et name
     *
     *
     * @param array $array The input array of associative arrays to be transformed.
     * @param string $value_field The field name to be used as the value for each item. Defaults to 'name'.
     * @param string $key_field The field name to be used as the key for each item. Defaults to 'id'.
     *
     * @return array The formatted array representing a select list structure, with each item having 'id' and 'name' keys.
     */
    public static function asSelectList(array $array, string $value_field = 'name', string $key_field = 'id'): array
    {
        $list = [];
        foreach ($array as $item) {
            $item = [
                'id' => $item[$key_field],
                'name' => $item[$value_field]
            ];
            $list[] = $item;
        }
        return $list;
    }

    public static function redirect($url): void
    {
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");
        header('Location: ' . $url);
        exit;
    }
}