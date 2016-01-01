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

    public function upgrade($path)
    {
        $src_path = $path . '/yeswiki';
        $des_path = $this->getWikiDir();

        $file_to_ignore = array(
            '.',
            '..',
            'tools',
            'files',
            'cache',
            'themes',
            'wakka.config.php',
        );

        return $this->upgradeFolder($src_path, $des_path, $file_to_ignore);
    }

    public function upgradeTools($path)
    {
        return false;
    }

    private function upgradeTool($name, $path)
    {
        return false;
    }

    public function getWikiVersion()
    {
        if (isset($this->wiki->config['wikini_version'])) {
            return $this->wiki->config['wikini_version'];
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

    private function upgradeFolder($src, $des, $file_to_ignore = null)
    {

        if (is_file($src)) {
            return copy($src, $des);
        }

        if (!is_array($file_to_ignore)) {
            $file_to_ignore = array('.', '..');
        }

        if ($res = opendir($src)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (in_array($file, $file_to_ignore)) {
                    continue;
                }

                $src_file = $src . '/' . $file;
                $des_file = $des . '/' . $file;

                if (is_dir($des_file) === true) {
                    $this->deleteFolder($des_file);
                } else {
                    unlink($des_file);
                }

                rename($src_file, $src_file);

            }
            closedir($res);
        }

        return true;
    }

    private function deleteFolder($path, $depth = 0)
    {
        $file_to_ignore = array('.', '..');

        if ($res = opendir($path)) {
            while (($file = readdir($res)) !== false) {

                if (in_array($file, $file_to_ignore)) {
                    continue;
                }

                $file = $path . '/' . $file;

                if (is_dir($file) === true) {
                    $this->deleteFolder($file, $depth + 1);
                }

                if (is_file($file) === true) {
                    unlink($file);
                }
            }
            closedir($res);
        }
        rmdir($path);
    }
}
