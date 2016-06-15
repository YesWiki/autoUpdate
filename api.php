<?php
namespace AutoUpdate;

$loader = require __DIR__ . '/vendor/autoload.php';

// hack pour éviter le plantage lors de l'appel a _t() TODO faire plus propre
function _t($textkey)
{
    return $textkey;
}

$autoUpdate = new AutoUpdate(
    new Configuration('../../wakka.config.php'),
    false // TODO : Check token and set true if is valid
);

$controller = new ApiController($autoUpdate);

$controller->run(
    $_SERVER['REQUEST_METHOD'],
    new ApiRoute($_SERVER['QUERY_STRING'])
);
