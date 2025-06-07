<?php


use OsdAurox\Base;
use OsdAurox\Modal;

$title = 'Aurox';

require_once '../aurox.php';


?>
<?php require('../templates/header.php'); ?>
<main class="py-4">
    <h1>Accueil</h1>
    <p>Contenu de la page</p>

    <h3>Detection des petits écran Base::isMobile()</h3>

    <?php if (Base::isMobile()): ?>
        <p>C'est un petit écran</p>
    <?php else: ?>
        <p>C'est un grand écran</p>
    <?php endif; ?>

</main>
<?php require('../templates/footer.php'); ?>
