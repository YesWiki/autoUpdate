<?php
namespace AutoUpdate;

/**
 * Classe UserController
 *
 * gère les utilisateurs
 * @package AutoUpdate
 * @author  Florestan Bredow <florestan.bredow@supagro.fr>
 * @version 0.0.1 (Git: $Id$)
 * @copyright 2015 Florestan Bredow
 */
class UserController
{
    /**
     * Contient la configuration de la ferme.
     * @var Configuration
     */
    private $configuration;

    /**
     * Constructeur
     * @param Configuration $configuration Contient la configuration de la ferme.
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Vérifie les identifiants d'un utilisateur et le connecte si ils sont bon.
     * @param  string $username identifiant a tester
     * @param  string $password mot de passe a tester
     * @return bool             Vrai si la connexion a réussie, faux sinon.
     */
    public function login($username, $password)
    {

    }

    /**
     * Supprime informations concernant la connexion dans $_session
     */
    public function logout()
    {
        foreach (array('username', 'logged') as $value) {
            if (isset($_SESSION[$value])) {
                unset($_SESSION[$value]);
            }
        }
    }

    /**
     * Détermine si un utilisateur est connecté
     * @return boolean Vrai si un utilisateur est connecté, faux sinon.
     */
    public function isLogged()
    {
        if (isset($_SESSION['username'])
            and isset($_SESSION['logged'])
            and true == $_SESSION
        ) {
            return true;
        }
        return false;
    }
}
