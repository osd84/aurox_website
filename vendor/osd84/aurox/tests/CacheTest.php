<?php

require_once '../aurox.php';

use OsdAurox\Cache;
use osd84\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);


$cache = new Cache();
$cache->set('test', 'MyLittlePoney', 60);
$out  = $cache->get('test');
$tester->assertEqual( $out, 'MyLittlePoney', 'Cache ok');

$cache->delete('test');
$out  = $cache->get('test');
$tester->assertEqual( $out, false, 'Cache delete ok');

$cache->set('test', 'MyLittlePoney', 60);
$cache->clear();
$out  = $cache->get('test');
$tester->assertEqual( $out, false, 'Cache clear ok');

$tester->footer(exit: false);