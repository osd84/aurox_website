<?php


use OsdAurox\Base;
use OsdAurox\Modal;

$title = 'Aurox';

require_once '../aurox.php';


?>
<?php require('../templates/header.php'); ?>
<main class="py-4">
    <h1>Accueil</h1>
    <p>
        Cette mini application sert à tester les fonctionnalités de base de l'application côté FRONT. <br>
        Et les tests unitaires. <br>
        Pour avoir de vrais starter pack voir sur <a href="https://aurox.fr">La Documentation</a>
    </p>

    <h3>Detection des petits écran Base::isMobile()</h3>

    <?php if (Base::isMobile()): ?>
        <p>C'est un petit écran</p>
    <?php else: ?>
        <p>C'est un grand écran</p>
    <?php endif; ?>

</main>
<?php require('../templates/footer.php'); ?>
