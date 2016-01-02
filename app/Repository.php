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
    public function compareVersion($localVersion, $repoVersion = 'stable')
    {
        if ($localVersion === $this->data[$repoVersion]['version']) {
            return 0;
        }

        $repoVersion = $this->evalVersion(
            $this->data[$repoVersion]['version']
        );

        $localVersion = $this->evalVersion(
            $localVersion
        );

        for ($i = 0; $i < 3; $i++) {
            if ($repoVersion[$i] > $localVersion[$i]) {
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

        $destinationFile = tempnam(sys_get_temp_dir(), 'yeswiki_');
        $sourceUrl = $this->address . $this->data[$version]['file'];

        $this->downloadFile(
            $sourceUrl,
            $destinationFile
        );

        if (is_file($destinationFile)) {
            return $destinationFile;
        }

        return false;
    }

    private function loadRepo()
    {
        $this->data = array();

        $repoInfosFile = $this->address . 'infos.json';
        if (($repoInfos = file_get_contents($repoInfosFile)) === false) {
            return false;
        }

        $this->data = json_decode($repoInfos, true);

        if (is_null($this->data)) {
            return false;
        }

        return $this->data;
    }

    private function evalVersion($version)
    {
        return explode('.', $version);
    }

    private function downloadFile($sourceUrl, $destination)
    {
        file_put_contents(
            $destination,
            fopen($sourceUrl, 'r')
        );
    }
}
