<?php
namespace AutoUpdate;

class AutoUpdate
{
    const DEFAULT_REPO = 'http://yeswiki.net/repository/stable/';
    const DEFAULT_VERS = 'Cercopitheque';

    private $wiki;
    private $files = null;
    public $repository = null;

    public function __construct($wiki)
    {
        $this->wiki = $wiki;
        $this->files = new Files();
    }

    public function initRepository()
    {
        $this->repository = new Repository($this->getRepositoryAddress());
        return $this->repository->load();
    }

    public function isAdmin()
    {
        return $this->wiki->UserIsAdmin();
    }

    public function extract($file)
    {
        $path = $this->files->tmpdir();

        $zip = new \ZipArchive;
        if (true !== $zip->open($file)) {
            return false;
        }
        if (true !== $zip->extractTo($path)) {
            return false;
        }
        $zip->close();

        return $path;
    }

    public function checkFilesACL()
    {
        $path = $this->getWikiDir();
        return $this->files->isWritable($path);
    }

    public function checkIntegrity($path)
    {
        $repoMD5 = $this->repository->getMD5();
        $md5File = md5_file($path);
        return ($md5File === $repoMD5);
    }

    public function upgradeCore($path)
    {
        $src = $path . '/yeswiki';
        $des = $this->getWikiDir();

        $file2ignore = array('.', '..', 'tools', 'files', 'cache', 'themes',
            'wakka.config.php');

        if ($res = opendir($src)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (!in_array($file, $file2ignore)) {
                    $this->files->copy($src . '/' . $file, $des . '/' . $file);
                }
            }
            closedir($res);
        }
        return true;
    }

    public function upgradeConf()
    {
        $conf = new Configuration($this->getWikiDir() . '/wakka.config.php');
        $conf['yeswiki_release'] = $this->repository->getVersion();
        return $conf->write();
    }

    public function upgradeTools($path)
    {
        $src = $path . '/yeswiki/tools';
        $des = $this->getWikiDir() . '/tools';
        $file2ignore = array('.', '..');

        // TODO : Ajouter un message par outils mis à jour.
        if ($res = opendir($src)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (!in_array($file, $file2ignore)) {
                    $this->files->copy($src . '/' . $file, $des . '/' . $file);
                }
            }
            closedir($res);
        }
        return true;
    }

    public function getYesWikiRelease()
    {
        if (isset($this->wiki->config['yeswiki_release'])) {
            return $this->wiki->config['yeswiki_release'];
        }
        return _t('AU_UNKNOW');
    }


    public function isNewVersion()
    {
        if ($this->repository->compareVersion($this->getYesWikiRelease()) > 0) {
            return true;
        }
        return false;
    }

    private function getRepositoryAddress()
    {
        $repositoryAddress = $this::DEFAULT_REPO;

        if (isset($this->wiki->config['yeswiki_repository'])) {
            $repositoryAddress = $this->wiki->config['yeswiki_repository'];
        }

        if (substr($repositoryAddress, -1, 1) !== '/') {
            $repositoryAddress .= '/';
        }

        $repositoryAddress .= $this->getYesWikiVersion();
        return $repositoryAddress;
    }

    private function getYesWikiVersion()
    {
        $version = $this::DEFAULT_VERS;
        if (isset($this->wiki->config['yeswiki_version'])) {
            $version = $this->wiki->config['yeswiki_version'];
        }
        return strtolower($version);
    }

    private function getWikiDir()
    {
        return dirname(dirname(dirname(__DIR__)));
    }
}
