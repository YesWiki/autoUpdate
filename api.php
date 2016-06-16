<?php
namespace AutoUpdate;

$loader = require __DIR__ . '/vendor/autoload.php';

// hack pour éviter le plantage lors de l'appel a _t() TODO faire plus propre
function _t($textkey)
{
    return $textkey;
}

$configuration = new Configuration('wakka.config.php');
$configuration->load();

 // TODO : Check token and set true if is valid
$autoUpdate = new AutoUpdate($configuration, false);

$controller = new ApiController($autoUpdate);

$controller->run(
    $_SERVER['REQUEST_METHOD'],
    new ApiRoute($_SERVER['QUERY_STRING'])
);
