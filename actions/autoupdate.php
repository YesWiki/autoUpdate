<?php
namespace AutoUpdate;

$loader = require __DIR__ . '/../vendor/autoload.php';

if (!defined("WIKINI_VERSION")) {
    die("acc&egrave;s direct interdit");
}

$controller = new Controller($this);
$controller->run();
