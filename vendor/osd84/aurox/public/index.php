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


    <h3>JS : osd-bp.js</h3>
    <p id="breakpoint-out" style="color: grey; border: 1px solid black; width: fit-content" class="p-4">

    </p>
</main>
<script>

    // Test de osd-bp.js
    $(document).ready(function(){

        // Utilitaire JS pour gérer les breakpoints Bootstrap
        console.log('getBootstrapBreakpoint BP is :', bpGetCurrent());
        console.log('getBootstrapBreakpoint BP is :', bpGetCurrent(true));
        console.log('bpTranslate BP is :', bpTranslate('xl'));

        $('#breakpoint-out').html(
            'breakpoint-out : ' + bpGetCurrent() + '<br>' +
            'breakpoint-out : ' + bpGetCurrent(true) + '<br>' +
            'breakpoint-out : ' + bpTranslate('xl')
        );

    })

</script>
<?php require('../templates/footer.php'); ?>
