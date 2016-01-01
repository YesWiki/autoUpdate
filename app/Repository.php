<?php
namespace AutoUpdate;

class Repository
{
    const DEFAULT_REPO = 'http://yeswiki.net/repository/';

    private $address;
    private $data = null;

    public function __construct($address = DEFAULT_REPO)
    {
        $this->address = $address;
        $this->loadRepo();
    }

    /**
     * [compareVersion description]
     * @param  [type] $local_version [description]
     * @param  string $repo_version  [description]
     * @return [type]                [description]
     */
    public function compareVersion($local_version, $repo_version = 'stable')
    {
        if ($local_version === $this->data[$repo_version]['version']) {
            return 0;
        }

        $repo_version = $this->evalVersion(
            $this->data[$repo_version]['version']
        );

        $local_version = $this->evalVersion(
            $local_version
        );

        for ($i = 0; $i < 3; $i++) {
            if ($repo_version[$i] > $local_version[$i]) {
                return $i + 1;
            }
        }
        return -1;
    }

    public function getFile($version = 'stable')
    {
        if ('unstable' !== $version) {
            $version = 'stable';
        }

        $destination_file = tempnam(sys_get_temp_dir(), 'yeswiki_');
        $source_url = $this->address . $this->data[$version]['file'];

        $this->downloadFile(
            $source_url,
            $destination_file
        );

        if (is_file($destination_file)) {
            return $destination_file;
        }

        return false;
    }

    private function loadRepo()
    {
        $this->data = array();

        $repo_info_file = $this->address . 'infos.json';
        if (($repo_info = file_get_contents($repo_info_file)) === false) {
            return false;
        }

        $this->data = json_decode($repo_info, true);

        if (is_null($this->data)) {
            return false;
        }

        return $this->data;
    }

    private function evalVersion($version)
    {
        return explode('.', $version);
    }

    private function downloadFile($source_url, $destination)
    {
        file_put_contents(
            $destination,
            fopen($source_url, 'r')
        );
    }
}
