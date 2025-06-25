<?php


use App\AppUrls;

$title = 'OSD_Aurox™ - Mobile';


require_once '../aurox.php';

?>
<?php require('../templates/header.php'); ?>
<main class="py-4">
    <h1>Exemple de requête Ajax</h1>

    <p>
        Doc : <a href="https://aurox.fr/doc.php#ajax" target="_blank">Ajax</a>
    </p>

    <ul>
        <li>
            <a href="<?= AppUrls::AJAX_ROULETTE ?>" target="_blank">AJAX_ROULETTE</a>
        </li>
    </ul>


</main>
<?php require('../templates/footer.php'); ?>
