<?php
namespace AutoUpdate;

/**
 * Classe Controller
 *
 * gère les entrées ($_POST et $get)
 * @package AutoUpload
 * @author  Florestan Bredow <florestan.bredow@supagro.fr>
 * @version 0.0.1 (Git: $Id$)
 * @copyright 2015 Florestan Bredow
 */
class Controller
{
    private $autoupload;
    private $messages;

    public function __construct($wikiInstance)
    {
        $this->autoupload = new AutoUpdate($wikiInstance);
        $this->messages = new Messages($wikiInstance);
    }

    public function run($get)
    {
        $view = new View($this->autoupload);

        if (!isset($get['autoupdate'])) {
            $get['autoupdate'] = "default";
        }

        switch ($get['autoupdate']) {
            case 'upgrade':
                if ($this->autoupload->isAdmin()) {

                    // Remise a zéro des messages
                    $this->messages->reset();

                    // Télécahrgement de l'archive
                    $file = $this->autoupload->download();
                    if (false === $file) {
                        $this->messages->add('AU_DOWNLOAD', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_DOWNLOAD', 'AU_OK');

                    // Vérification MD5
                    // TODO

                    // Extraction de l'archive
                    $path = $this->autoupload->extract($file);
                    if (false === $path) {
                        $this->messages->add('AU_EXTRACT', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_EXTRACT', 'AU_OK');

                    // Mise à jour du coeur du wiki
                    if (!$this->autoupload->upgrade($path)) {
                        $this->messages->add('AU_UPDATE_YESWIKI', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_UPDATE_YESWIKI', 'AU_OK');

                    // Mise à jour des tools.
                    /*if (!$this->autoupload->upgradeTools($path)) {
                        $this->messages->add('AU_UPDATE_TOOL', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_UPDATE_TOOL', 'AU_OK');*/

                    $view->show('update');
                }
                break;

            default:
                $view->show('status');
                break;
        }
    }
}
