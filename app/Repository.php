<?php
namespace AutoUpdate;

class Repository
{
    const DEFAULT_REPO = 'http://yeswiki.net/repository/stable/';

    private $address;
    private $data = null;

    public function __construct($address = null)
    {
        if (is_null($address)) {
            $address = $this::DEFAULT_REPO;
        }
        $this->address = $address;
    }

    /**
     * [compareVersion description]
     * @param  [type] $local_version [description]
     * @param  string $repo_version  [description]
     * @return [type]                [description]
     */
    public function compareVersion($localVersion, $repoVersion = 'stable')
    {
        $repoVersion = $this->getVersion($repoVersion);

        if ($localVersion === $repoVersion) {
            return 0;
        }

        $repoVersion = $this->evalVersion($repoVersion);

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

    public function getVersion($repoVersion = 'stable')
    {
        return $this->data[$repoVersion]['version'];
    }

    public function getMD5($version = 'stable')
    {
        $disMd5File = file_get_contents(
            $this->address . $this->data[$version]['file'] . '.md5'
        );
        return explode('  ', $disMd5File)[0];
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

    public function load()
    {
        if (filter_var($this->address, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $this->data = array();

        $repoInfosFile = $this->address . 'infos.json';
        if (($repoInfos = @file_get_contents($repoInfosFile)) === false) {
            return false;
        }

        $this->data = json_decode($repoInfos, true);

        if (is_null($this->data)) {
            return false;
        }

        return true;
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
