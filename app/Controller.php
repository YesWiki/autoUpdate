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
    private $autoUpdate;
    private $messages;

    public function __construct($wikiInstance)
    {
        $this->autoUpdate = new AutoUpdate($wikiInstance);
        $this->messages = new Messages($wikiInstance);
    }

    public function run($get)
    {
        $view = new View($this->autoUpdate, $this->messages);

        if (!isset($get['autoupdate'])) {
            $get['autoupdate'] = "default";
        }

        switch ($get['autoupdate']) {
            case 'upgrade':
                if ($this->autoUpdate->isAdmin()) {
                    // Remise a zéro des messages
                    $this->messages->reset();

                    // Télécahrgement de l'archive
                    $file = $this->autoUpdate->download();
                    if (false === $file) {
                        $this->messages->add('AU_DOWNLOAD', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_DOWNLOAD', 'AU_OK');

                    // Vérification MD5
                    if (!$this->autoUpdate->checkIntegrity($file)) {
                        $this->messages->add('AU_INTEGRITY', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_INTEGRITY', 'AU_OK');

                    // Extraction de l'archive
                    $path = $this->autoUpdate->extract($file);
                    if (false === $path) {
                        $this->messages->add('AU_EXTRACT', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_EXTRACT', 'AU_OK');

                    // Vérification des droits sur le fichiers
                    if (!$this->autoUpdate->checkFilesACL($path)) {
                        $this->messages->add('AU_ACL', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_ACL', 'AU_OK');

                    // Mise à jour du coeur du wiki
                    if (!$this->autoUpdate->upgradeCore($path)) {
                        $this->messages->add('AU_UPDATE_YESWIKI', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_UPDATE_YESWIKI', 'AU_OK');

                    // Mise à jour du coeur du wiki
                    if (!$this->autoUpdate->upgradeConf()) {
                        $this->messages->add('AU_UPDATE_CONF', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_UPDATE_CONF', 'AU_OK');

                    // Mise à jour des tools.
                    if (!$this->autoUpdate->upgradeTools($path)) {
                        $this->messages->add('AU_UPDATE_TOOL', 'AU_ERROR');
                        $view->show('update');
                        break;
                    }
                    $this->messages->add('AU_UPDATE_TOOL', 'AU_OK');

                    $view->show('update');
                }
                break;

            default:
                $view->show('status');
                break;
        }
    }
}
