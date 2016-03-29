<?php
namespace AutoUpdate;

class Repository
{
    const INDEX_FILENAME = 'packages.json';

    private $address;
    public $packages = null;

    public function __construct($address)
    {
        $this->address = $address;
    }

    public function load()
    {
        $this->address .= '/';
        $this->packages = new PackageCollection();

        $data = array();

        if (filter_var($this->address, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $repoInfosFile = $this->address . $this::INDEX_FILENAME;

        if (($repoInfos = @file_get_contents($repoInfosFile)) === false) {
            return false;
        }

        $data = json_decode($repoInfos, true);

        if (is_null($data)) {
            return false;
        }

        $this->packages = new PackageCollection();

        foreach ($data as $packageInfos) {
            $version = new Version($packageInfos['version']);
            $this->packages->add(
                $version,
                $this->address,
                $packageInfos['file']
            );
        }

        return true;
    }

    public function getPackage($name)
    {
        if ($this->packages === null) {
            throw new \Exception("Liste des paquets non initialisée", 1);
        }

        if (isset($this->packages[$name])) {
            return $this->packages[$name];
        }

        return false;

    }
}
