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

    public function __construct($autoUpdate, $messages)
    {
        $this->autoUpdate = $autoUpdate;
        $this->messages = $messages;
    }

    public function run($get)
    {
        if (!isset($get['autoupdate'])) {
            $get['autoupdate'] = "default";
        }

        if (!$this->autoUpdate->initRepository()) {
            $view = new ViewNoRepo($this->autoUpdate);
            $view->show();
            return;
        }

        switch ($get['autoupdate']) {
            case 'upgrade':
                if ($this->autoUpdate->isAdmin()) {
                    $this->upgrade();
                    $view = new ViewUpdate($this->autoUpdate, $this->messages);
                    $view->show();
                }
                break;

            default:
                $view = new ViewStatus($this->autoUpdate, $this->messages);
                $view->show();
                break;
        }
    }

    private function upgrade()
    {
        // Remise a zéro des messages
        $this->messages->reset();

        $corePackage = $this->autoUpdate->repository->getPackage('yeswiki');

        // Téléchargement de l'archive
        $file = $corePackage->getFile();
        if (false === $file) {
            $this->messages->add('AU_DOWNLOAD', 'AU_ERROR');
            return;
        }
        $this->messages->add('AU_DOWNLOAD', 'AU_OK');

        // Vérification MD5
        if (!$corePackage->checkIntegrity($file)) {
            $this->messages->add('AU_INTEGRITY', 'AU_ERROR');
            return;
        }
        $this->messages->add('AU_INTEGRITY', 'AU_OK');

        // Extraction de l'archive
        $path = $corePackage->extract();
        if (false === $path) {
            $this->messages->add('AU_EXTRACT', 'AU_ERROR');
            return;
        }
        $this->messages->add('AU_EXTRACT', 'AU_OK');

        // Vérification des droits sur le fichiers
        if (!$this->autoUpdate->checkFilesACL($path)) {
            $this->messages->add('AU_ACL', 'AU_ERROR');
            return;
        }
        $this->messages->add('AU_ACL', 'AU_OK');

        $wikiPath = $this->autoUpdate->getWikiDir();

        // Mise à jour du coeur du wiki
        if (!$corePackage->upgrade($wikiPath)) {
            $this->messages->add('AU_UPDATE_YESWIKI', 'AU_ERROR');
            return;
        }
        $this->messages->add('AU_UPDATE_YESWIKI', 'AU_OK');

        // Mise à jour du coeur du wiki
        if (!$corePackage->upgradeConf(
            $this->autoUpdate->getWikiConfiguration()
        )) {
            $this->messages->add('AU_UPDATE_CONF', 'AU_ERROR');
            return;
        }
        $this->messages->add('AU_UPDATE_CONF', 'AU_OK');

        // Mise à jour des tools.
        if (!$corePackage->upgradeTools($wikiPath)) {
            $this->messages->add('AU_UPDATE_TOOL', 'AU_ERROR');
            return;
        }
        $this->messages->add('AU_UPDATE_TOOL', 'AU_OK');
    }
}
