<?php

require_once '../aurox.php';

use OsdAurox\Base;
use osd84\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);


// init
if(!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(__DIR__));
}

$arrayForm = [
    ['id' => 1, 'name' => 'test1', 'other' => 'other'],
    ['id' => 2, 'name' => 'test2', 'fake' => 'fake'],
    ['id' => 3, 'name' => 'test3', 'span' => 'span'],
];
$out = Base::asSelectList($arrayForm);
$expect = ['id' => 1, 'name' => 'test1'];
$tester->assertEqual($out[0], $expect, 'asSelectList() ok');

$tester->footer(exit: false);