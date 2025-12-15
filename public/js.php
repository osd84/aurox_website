<?php


use App\AppUrls;
use OsdAurox\Flash;
use OsdAurox\FormValidator;
use OsdAurox\Base;
use OsdAurox\Forms;
use OsdAurox\I18n;
use OsdAurox\Modal;
use OsdAurox\Sec;
use OsdAurox\Validator;

$title = 'OSD_Aurox™ - Mobile';


require_once '../aurox.php';

?>
<?php require('../templates/header.php'); ?>
<main class="py-4">
    <h1>Librairie JS</h1>

    <h3>JS : osd-bp.js</h3>

    <p id="breakpoint-out" style="color: grey; border: 1px solid black; width: fit-content" class="p-4"></p>
    <a href="https://aurox.fr/doc.php#jsosdbp">voir la documentation</a>


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
