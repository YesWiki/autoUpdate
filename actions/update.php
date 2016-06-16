<?php
namespace AutoUpdate;

$loader = require __DIR__ . '/../vendor/autoload.php';

if (!defined("WIKINI_VERSION")) {
    die("acc&egrave;s direct interdit");
}

$configuration = new Configuration('wakka.config.php');
$configuration->load();

$autoUpdate = new AutoUpdate($configuration, $this->userIsAdmin());

$controller = new Controller(
    $autoUpdate,
    new Messages()
);

$controller->run($_GET, $this->getParameter('filter'));
