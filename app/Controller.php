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
    private $config;
    private $user_controller;
    private $db_connection;

    public function __construct()
    {
        $this->config = new Configuration('../../wakka.config.php');
        $this->user_controller = new UserController($this->config);
    }

    public function run()
    {

        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'login':
                    if (isset($_POST['username'])
                        and isset($_POST['password'])
                    ) {
                        $this->user_controller->login(
                            $_POST['username'],
                            $_POST['password']
                        );
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }

        $view = new View('auth.twig');
        $view->Show();
    }

    private function dbConnect()
    {
        $yeswiki_config = new Configuration('../../wakka.config.php');
        if (!is_null($this->db_connection)) {
            return $this->db_connection;
        }

        $dsn = 'mysql:host='
        . $this->yeswiki_config['mysql_host']
        . ';dbname='
        . $this->yeswiki_config['mysql_database']
            . ';';

        try {
            $this->db_connexion = new \PDO(
                $dsn,
                $this->yeswiki_config['db_user'],
                $this->yeswiki_config['db_password']
            );
            return $this->db_connexion;
        } catch (\PDOException $e) {
            throw new \Exception(
                "Impossible de se connecter à la base de donnée : "
                . $e->getMessage(),
                1
            );
        }

    }
}
