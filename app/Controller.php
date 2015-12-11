<?php
namespace AutoUpdate;

/**
 * Classe Controller
 *
 * gère les entrées ($_POST et $_GET)
 * @package AutoUpload
 * @author  Florestan Bredow <florestan.bredow@supagro.fr>
 * @version 0.0.1 (Git: $Id$)
 * @copyright 2015 Florestan Bredow
 */
class Controller
{

    public function __construct()
    {
        //..
    }

    public function run()
    {
        $view = new View('auth.twig');
        $view->Show();
    }
}
