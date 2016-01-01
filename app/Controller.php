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
    private $au;
    private $messages;

    public function __construct($wiki_instance)
    {
        $this->au = new AutoUpdate($wiki_instance);
        $this->messages = new Messages($wiki_instance);
    }

    public function run()
    {
        $view = new View($this->au);

        if (!isset($_GET['autoupdate'])) {
            $_GET['autoupdate'] = "default";
        }

        switch ($_GET['autoupdate']) {
            case 'upgrade':
                if ($this->au->isAdmin()) {

                    // Remise a zéro des messages
                    $this->messages->reset();

                    // Télécahrgement de l'archive
                    $file = $this->au->download();
                    if (false !== $file) {
                        $this->messages->add('AU_DOWNLOAD', 'AU_OK');

                    } else {
                        $this->messages->add('AU_DOWNLOAD', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }

                    // Vérification MD5
                    // TODO

                    // Extraction de l'archive
                    $path = $this->au->extract($file);
                    if (false !== $path) {
                        $this->messages->add('AU_EXTRACT', 'AU_OK');
                    } else {
                        $this->messages->add('AU_EXTRACT', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }

                    // Mise à jour du coeur du wiki
                    /*if ($this->au->upgrade($path)) {
                    $this->messages->add('AU_EXTRACT', 'AU_OK');
                    } else {
                    $this->messages->add('AU_EXTRACT', 'AU_ERROR');
                    $view->show('update');
                    }*/

                    // Mise à jour des tools.
                    /*if ($this->au->upgradeTools($path)) {
                    $this->messages->add('AU_EXTRACT', 'AU_OK');
                    } else {
                    $this->messages->add('AU_EXTRACT', 'AU_ERROR');
                    $view->show('update');
                    }*/
                    $view->show('update');
                }
                break;

            default:
                $view->show('status');
                break;
        }
    }

    private function reload($args = null)
    {
        $url = "?wiki=" . $_GET['wiki'];
        if (!is_null($args) and is_array($args)) {
            foreach ($args as $arg => $value) {
                $url .= '&' . $arg . '=' . $value;
            }
        }

        header("Location: " . $url);
        exit();
    }

}
