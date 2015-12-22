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
    private $wiki;

    public function __construct($wiki_instance)
    {

        $this->wiki = $wiki_instance;
    }

    public function run()
    {
        $view = new View($this->wiki);

        if (!isset($_GET['autoupdate'])) {
            $_GET['autoupdate'] = "default";
        }

        switch ($_GET['autoupdate']) {
            case 'download':
                if ($this->actionDownload()
                    and $this->wiki->UserIsAdmin()
                ) {
                    $view->show('download_ok');
                } else {
                    $view->show('download_error');
                }
                break;

            case 'updatetool':
                break;

            case 'update':
                break;

            default:
                $view->show('status');
                break;
        }
    }

    private function actionDownload()
    {
        return true;
    }
}
