<?php

use OsdAurox\Api;
use OsdAurox\Base;

require_once  __DIR__ . '/../../aurox.php';


$res = new Api();

$res->status = true; // status de transmission de requête tjs True si pas d'erreur de Logique metier

// random number
$random = rand(0, 6);
$result = '👌';
if ($random == 0) {
    $result = '💥';
}

$res->datas['tic'] = $result;
$res->datas['value'] = $random;
$res->returnJsonResponse();
Base::dieOrThrow();