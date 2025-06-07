<?php

require_once '../aurox.php';


use OsdAurox\AppConfig;
use osd84\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);


$tester->assertEqual( AppConfig::get('appName'), 'OsdAurox', 'AppConfig ok');

$instance = AppConfig::getInstance();
$tester->assertEqual( $instance->appName, 'OsdAurox', 'AppConfig singleton ok');

// test si debug
$tester->assertEqual( AppConfig::isDebug(), true, 'AppConfig debug ok');

$tester->footer(exit: false);