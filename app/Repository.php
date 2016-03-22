<?php
namespace AutoUpdate;

class Repository
{
    const INDEX_FILE_NAME = 'packages.json';

    private $address;
    private $data = null;

    public function __construct($address)
    {
        $this->address = $address;
    }

    public function load()
    {
        $this->address .= '/';
        if (filter_var($this->address, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $this->data = array();

        $repoInfosFile = $this->address . $this::INDEX_FILE_NAME;

        if (($repoInfos = @file_get_contents($repoInfosFile)) === false) {
            return false;
        }

        $this->data = json_decode($repoInfos, true);

        if (is_null($this->data)) {
            return false;
        }

        return true;
    }

    /**
     * [compareVersion description]
     * @param  [type] $local_version [description]
     * @param  string $repo_version  [description]
     * @return [type]                [description]
     */
    public function compareVersion($localVersion)
    {
        $repoVersion = $this->getVersion();

        if ($localVersion === $repoVersion) {
            return 0;
        }

        $repoVersion = $this->evalVersion($repoVersion);
        $localVersion = $this->evalVersion($localVersion);

        for ($i = 0; $i < 4; $i++) {
            if ($repoVersion[$i] > $localVersion[$i]) {
                return $i + 1;
            }
        }
        return -1;
    }

    public function getVersion()
    {
        $pattern = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{1}$/";
        if (preg_match($pattern, $this->data['yeswiki']['version'])) {
                return $this->data['yeswiki']['version'];
        }
        return "0000-00-00-0";
    }

    public function getMD5()
    {
        $disMd5File = file_get_contents(
            $this->address . $this->data['yeswiki']['file'] . '.md5'
        );
        return explode('  ', $disMd5File)[0];
    }

    public function getFile()
    {

        $destinationFile = tempnam(sys_get_temp_dir(), 'yeswiki_');
        $sourceUrl = $this->address . $this->data['yeswiki']['file'];

        $this->downloadFile(
            $sourceUrl,
            $destinationFile
        );

        if (is_file($destinationFile)) {
            return $destinationFile;
        }

        return false;
    }

    private function evalVersion($version)
    {
        return explode('-', $version);
    }

    private function downloadFile($sourceUrl, $destination)
    {
        file_put_contents(
            $destination,
            fopen($sourceUrl, 'r')
        );
    }
}
