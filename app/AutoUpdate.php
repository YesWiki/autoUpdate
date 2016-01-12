<?php
namespace AutoUpdate;

class AutoUpdate
{
    private $wiki;
    private $files;

    public function __construct($wiki)
    {
        $this->wiki = $wiki;
        $this->files = new Files();
    }

    public function isAdmin()
    {
        return $this->wiki->UserIsAdmin();
    }

    public function download()
    {
        $repo = new Repository($this->getRepositoryAddress());
        return $repo->getFile();
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
        $repo = new Repository($this->getRepositoryAddress());
        $repoMD5 = $repo->getMD5();
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
        $repo = new Repository($this->getRepositoryAddress());
        $conf['yeswiki_release'] = $repo->getVersion();
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

    public function getWikiVersion()
    {
        if (isset($this->wiki->config['yeswiki_release'])) {
            return $this->wiki->config['yeswiki_release'];
        }
        return _t('AU_UNKNOW');
    }

    public function getRepoVersion()
    {
        $repo = new Repository($this->getRepositoryAddress());
        return $repo->getVersion();
    }

    public function isNewVersion()
    {
        $repo = new Repository($this->getRepositoryAddress());

        if ($repo->compareVersion($this->getWikiVersion()) > 0) {
            return true;
        }
        return false;
    }

    private function getRepositoryAddress()
    {
        $repository = null;
        if (isset($this->wiki->config['yeswiki_repository'])) {
            return $this->wiki->config['yeswiki_repository'];
        }
        return $repository;
    }

    private function getWikiDir()
    {
        return dirname(dirname(dirname(__DIR__)));
    }
}
