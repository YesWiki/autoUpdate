<?php
namespace AutoUpdate;

abstract class PackageExt extends Package
{
    const INFOS_FILENAME = "infos.json";

    protected $infos = null;

    public function upgrade()
    {
        $desPath = $this->localPath();

        $this->deletePackage();
        mkdir($desPath);

        if ($this->tmpPath === null) {
            throw new \Exception("Le paquet n'a pas été décompressé.", 1);
        }

        $this->copy(
            $this->tmpPath . '/' . $this->name(),
            $desPath
        );

        return true;
    }

    public function deletePackage()
    {
        $desPath = $this->localPath();
        if (is_dir($desPath)) {
            $this->delete($desPath);
        }
    }

    protected function getInfos()
    {
        if ($this->infos !== null) {
            return $this->infos;
        }

        $file = $this->localPath() . $this::INFOS_FILENAME;
        $this->infos = array();
        if (is_file($file)) {
            $json = file_get_contents($file);
            $this->infos = json_decode($json, true);
        }
        return $this->infos;
    }

    protected function localRelease()
    {
        if ($this->installed()) {
            $infos = $this->getInfos();
            if (isset($infos['release'])) {
                return $infos['release'];
            }
        }
        return new Release(Release::UNKNOW_RELEASE);
    }

    protected function installed()
    {
        if (is_dir($this->localPath())) {
            return true;
        }
        return false;
    }

    protected function updateAvailable()
    {
        if ($this->installed()) {
            if ($this->release->compare($this->localRelease()) > 0) {
                return true;
            }
        }
        return false;
    }
}
