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
        $src = $path . '/yeswiki';
        $des = $this->getWikiDir();

        $file2ignore = array(
            '.',
            '..',
            'tools',
            'files',
            'cache',
            'themes',
            'wakka.config.php',
        );

        return $this->upgradeFolder($src, $des, $file2ignore);
    }

    public function upgradeTools($path)
    {
        $src = $path . '/yeswiki/tools';
        $des = $this->getWikiDir() . '/tools';

        return $this->upgradeFolder($src, $des);
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

    private function upgradeFolder($srcPath, $desPath, $file2ignore = null)
    {

        if (is_file($srcPath)) {
            return copy($srcPath, $desPath);
        }

        if (!is_array($file2ignore)) {
            $file2ignore = array('.', '..');
        }

        if ($res = opendir($srcPath)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (in_array($file, $file2ignore)) {
                    continue;
                }

                $srcFile = $srcPath . '/' . $file;
                $desFile = $desPath . '/' . $file;

                if (is_dir($desFile) === true) {
                    $this->deleteFolder($desFile);
                } elseif (isFile($desFile) === true) {
                    unlink($desFile);
                }

                print('<pre>');
                print_r($srcFile);
                print(" vers : ");
                print_r($desFile);
                print('</pre>');

                rename($srcFile, $desFile);

            }
            closedir($res);
        }

        return true;
    }

    private function deleteFolder($path, $depth = 0)
    {
        $file2ignore = array('.', '..');

        if ($res = opendir($path)) {
            while (($file = readdir($res)) !== false) {

                if (in_array($file, $file2ignore)) {
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

    // Necessaire car la fonction rename ne fonctionne pas entre plusieurs
    // partitions
    private function copyFolder($srcPath, $desPath, $depth = 0)
    {
        $file2ignore = array('.', '..');

        if ($res = opendir($srcPath)) {
            while (($file = readdir($res)) !== false) {

                if (in_array($file, $file2ignore)) {
                    continue;
                }

                $srcFile = $srcPath . '/' . $file;
                $desFile = $desPath . '/' . $file;

                if (is_dir($srcFile) === true) {
                    $this->copyFolder($srcFile, $desFile, $depth + 1);
                }

                if (isFile($file) === true) {
                    copy($srcFile, $desPath);
                }
            }
            closedir($res);
        }
    }
}
