<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
    die('Illegal request');
}

use App\AppUrls;
use OsdAurox\Sec;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= Sec::hNoHtml($title ?? "OSD_Aurox™") ?></title>

    <!-- Jquery et oui ! encore et toujours -->
    <script src="/js/jquery-3.7.1.min.js"></script>

    <!-- Boostrap -->
    <link rel="stylesheet" href="/plugin/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <!-- Boostrap Js -->
    <script src="/plugin//bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>

    <!-- OSD_Aurox scripts -->
    <script src="/js/osd-modal-loader.js"></script>
    <script src="/js/osd-modal-alert.js"></script>
    <script src="/js/osd-modal-confirm.js"></script>

    <!-- Select2 -->
    <link href="/plugin/select2/css/select2.min.css" rel="stylesheet" />
    <script src="/plugin/select2/js/select2.min.js"></script>

    <!-- Ajoutez vos scripts -->


    <!-- Ajoutez vos feuilles de style -->
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">

    <header class="py-3 border-bottom bg-white shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="mb-0 fs-4"><?= Sec::hNoHtml($headerTitle ?? "OSD_Aurox™ - Website Starter Pack"); ?></h1>
        </div>

        <nav class="navbar navbar-expand-lg navbar-light bg-light mt-2 border rounded">
            <div class="container-fluid">
                <!-- Marque visible sur mobile -->
                <a class="navbar-brand fw-bold d-lg-none" href="<?= Sec::hNoHtml(AppUrls::HOME) ?>">
                    OSD_Aurox™
                </a>

                <!-- Bouton hamburger -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav"
                        aria-controls="siteNav" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Liens -->
                <div class="collapse navbar-collapse justify-content-end" id="siteNav">
                    <ul class="navbar-nav text-center">
                        <li class="nav-item"><a class="nav-link py-2" href="<?= Sec::hNoHtml(AppUrls::HOME) ?>">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link py-2" href="<?= Sec::hNoHtml(AppUrls::PAGE_FORMS) ?>">Formulaires</a></li>
                        <li class="nav-item"><a class="nav-link py-2" href="<?= Sec::hNoHtml(AppUrls::PAGE_MODALS) ?>">Modales</a></li>
                        <li class="nav-item"><a class="nav-link py-2" href="<?= Sec::hNoHtml(AppUrls::PAGE_MOBILE) ?>">Mobiles</a></li>
                        <li class="nav-item"><a class="nav-link py-2" href="<?= Sec::hNoHtml(AppUrls::PAGE_AJAX) ?>">Ajax</a></li>
                        <li class="nav-item d-none d-lg-block"><span class="nav-link disabled px-1">|</span></li>
                        <li class="nav-item"><a class="nav-link py-2" href="<?= Sec::hNoHtml(AppUrls::NOT_FOUND) ?>">404</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <hr>
    <?php require_once APP_ROOT . '/templates/helpers/flash.php'; ?>