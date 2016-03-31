<?php
namespace AutoUpdate;

abstract class PackageExt extends Package
{
    const INFOS_FILENAME = "infos.json";

    protected $infos = null;

    abstract protected function localPath();

    public function updateAvailable()
    {
        if ($this->installed()) {
            if ($this->release->compare($this->localRelease()) > 0) {
                return true;
            }
        }
        return false;
    }

    public function localRelease()
    {
        if ($this->installed()) {
            $infos = $this->getInfos();
            if (isset($infos['release'])) {
                return $infos['release'];
            }
        }
        return new Release(Release::UNKNOW_RELEASE);
    }

    public function installed()
    {
        if (is_dir($this->localPath())) {
            return true;
        }
        return false;
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
}
