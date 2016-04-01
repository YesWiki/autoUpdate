<?php
namespace AutoUpdate;

abstract class PackageExt extends Package
{
    const INFOS_FILENAME = "infos.json";

    protected $infos = null;

    abstract protected function localPath();

    public function __construct($release, $address)
    {
        parent::__construct($release, $address);
        $this->installed = $this->installed();
        $this->localPath = dirname(dirname(dirname(__DIR__)));
        $this->updateAvailable = $this->updateAvailable();
    }

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

    private function installed()
    {
        if (is_dir($this->localPath())) {
            return true;
        }
        return false;
    }
}
