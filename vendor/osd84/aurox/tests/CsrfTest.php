<?php

require_once '../aurox.php';

use OsdAurox\Csrf;
use osd84\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);


// init
if(!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(__DIR__));
}

$_SERVER['REQUEST_METHOD'] = 'POST'; // mock

$_SESSION['csrf'] = Csrf::generate();
$_POST['_csrf'] = $_SESSION['csrf'];
$csrf = Csrf::protect();
$tester->assertEqual(strlen($csrf) > 50, true, 'Csrf is ok');
$tester->assertEqual($csrf, $_SESSION['csrf'] ?? null, 'Csrf dans Session');

$_SERVER['REQUEST_METHOD'] = 'POST'; // mock
$fail = Csrf::verify($csrf);
$tester->assertEqual($fail, false, 'Csrf false');

$_SERVER['REQUEST_METHOD'] = 'POST'; // mock
$_SESSION['csrf'] = Csrf::generate();
$_POST['_csrf'] = $_SESSION['csrf'];// mock
$success = Csrf::verify($_SESSION['csrf']);
$tester->assertEqual($success, true, 'Csrf ok');

$csrf_html = Csrf::inputHtml();
$tester->assertEqual(str_contains($csrf_html, $_SESSION['csrf']), true, 'Csrf input html ok');
$tester->assertEqual(str_contains($csrf_html, 'name="_csrf"'), true, 'Csrf input html ok');

$tester->footer(exit: false);