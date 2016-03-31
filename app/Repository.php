<?php
namespace AutoUpdate;

class Repository extends PackageCollection
{
    const INDEX_FILENAME = 'packages.json';

    private $address;

    public function __construct($address)
    {
        $this->address = $address . '/';
    }

    public function load()
    {
        $this->list = array();

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

        foreach ($data as $packageInfos) {
            $release = new Release($packageInfos['version']);
            $this->add(
                $release,
                $this->address,
                $packageInfos['file']
            );
        }

        return true;
    }
}
