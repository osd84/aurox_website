<?php

namespace OsdAurox;



class Filter
{
    public static function truncate($text, $length = 100, $ending = '...')
    {
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length - strlen($ending)) . $ending;
        }
        return $text;
    }

    public static function dateFr($date)
    {
        return Sec::h(date('d/m/Y', strtotime($date)));
    }

    public static function dateMonthFr($date)
    {
        $months = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        return Sec::h($months[$month] . ' ' . $year);
    }

    public static function dateUs($date)
    {
        return Sec::h(date('Y-m-d', strtotime($date)));
    }

    public static function toDayDateUs()
    {
        return Sec::h(date('Y-m-d'));
    }
}