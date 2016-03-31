<?php
namespace AutoUpdate;

class PackageCore extends Package
{
    const CORE_NAME = 'yeswiki';
    const FILE_2_IGNORE = array('.', '..', 'tools', 'files', 'cache', 'themes',
        'wakka.config.php');

    private $localRelease = null;

    public function updateAvailable()
    {
        if ($this->release->compare($this->localRelease()) > 0) {
            return true;
        }
        return false;
    }

    public function localRelease()
    {
        if ($this->localRelease !== null) {
            return $this->localRelease;
        }

        $configuration = new Configuration('wakka.config.php');
        $configuration->load();

        $release = Release::UNKNOW_RELEASE;
        if (isset($configuration['yeswiki_release'])) {
            $release = $configuration['yeswiki_release'];
        }
        $this->localRelease = new Release($release);
        return $this->localRelease;
    }

    public function upgrade($desPath)
    {
        if ($this->tmpPath === null) {
            throw new \Exception("Le paquet n'a pas été décompressé.", 1);
        }

        $this->tmpPath .= '/';
        if ($res = opendir($this->tmpPath)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (!in_array($file, $this::FILE_2_IGNORE)) {
                    $this->copy(
                        $this->tmpPath . '/' . $file,
                        $desPath . '/' . $file
                    );
                }
            }
            closedir($res);
        }
        return true;
    }

    public function installed()
    {
        return true;
    }

    public function upgradeTools($desPath)
    {
        $src = $this->tmpPath . '/tools';
        $desPath .= '/tools';
        $file2ignore = array('.', '..');
        // TODO : Ajouter un message par outils mis à jour.
        if ($res = opendir($src)) {
            while (($file = readdir($res)) !== false) {
                // Ignore les fichiers de la liste
                if (!in_array($file, $file2ignore)) {
                    $this->copy($src . '/' . $file, $desPath . '/' . $file);
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
