<?php
namespace AutoUpdate;

class AutoUpdate
{

    private $wiki;

    public function __construct($wiki)
    {
        $this->wiki = $wiki;
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
        $path = $this->tmpdir();

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
                    $this->copy($src . '/' . $file, $des . '/' . $file);
                }
            }
            closedir($res);
        }
        return true;
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
                    $this->copy($src . '/' . $file, $des . '/' . $file);
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

    private function tmpdir()
    {
        $path = tempnam(sys_get_temp_dir(), 'yeswiki_');

        if (is_file($path)) {
            unlink($path);
        }

        mkdir($path);
        return $path;
    }

    private function delete($path)
    {
        if (is_file($path)) {
            if (unlink($path)) {
                return true;
            }
            return false;
        }
        if (is_dir($path)) {
            return $this->deleteFolder($path);
        }
    }

    private function copy($src, $des)
    {
        if (is_file($des) or is_dir($des) or is_link($des)) {
            $this->delete($des);
        }
        if (is_file($src)) {
            return copy($src, $des);
        }
        if (is_dir($src)) {
            if (!mkdir($des)) {
                return false;
            }
            return $this->copyFolder($src, $des);
        }
        return false;
    }

    private function deleteFolder($path)
    {
        $file2ignore = array('.', '..');
        if ($res = opendir($path)) {
            while (($file = readdir($res)) !== false) {
                if (!in_array($file, $file2ignore)) {
                    $this->delete($path . '/' . $file);
                }
            }
            closedir($res);
        }
        rmdir($path);
        return true;
    }

    private function copyFolder($srcPath, $desPath)
    {
        $file2ignore = array('.', '..');
        if ($res = opendir($srcPath)) {
            while (($file = readdir($res)) !== false) {
                if (!in_array($file, $file2ignore)) {
                    $this->copy($srcPath . '/' . $file, $desPath . '/' . $file);
                }
            }
            closedir($res);
        }
        return true;
    }
}
