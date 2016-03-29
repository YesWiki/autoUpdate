<?php
namespace AutoUpdate;

class PackageCore extends Package
{
    const CORE_NAME = 'yeswiki';
    const FILE_2_IGNORE = array('.', '..', 'tools', 'files', 'cache', 'themes',
        'wakka.config.php');

    public function infos()
    {
        return array();
    }

    public function upgrade($desPath)
    {
        if ($this->tmpPath === null) {
            throw new \Exception("Le paquet n'a pas été décompressé.", 1);
        }

        $this->tmpPath .= '/';
        $files = new Files();
        if ($res = opendir($this->tmpPath)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (!in_array($file, $this::FILE_2_IGNORE)) {
                    $files->copy(
                        $this->tmpPath . '/' . $file,
                        $desPath . '/' . $file
                    );
                }
            }
            closedir($res);
        }
        return true;
    }

    public function upgradeTools($desPath)
    {
        $src = $this->tmpPath . '/tools';
        $desPath .= '/tools';
        $file2ignore = array('.', '..');
        $files = new Files();
        // TODO : Ajouter un message par outils mis à jour.
        if ($res = opendir($src)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (!in_array($file, $file2ignore)) {
                    $files->copy($src . '/' . $file, $desPath . '/' . $file);
                }
            }
            closedir($res);
        }
        return true;
    }

    public function upgradeConf($configuration)
    {
        $configuration['yeswiki_release'] = $this->version();
        return $configuration->write();
    }

    public function name()
    {
        return $this::CORE_NAME;
    }
}
