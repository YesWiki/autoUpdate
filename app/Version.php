<?php
namespace AutoUpdate;

class Version
{
    private $strVersion;

    public function __construct($strVersion)
    {
        $this->strVersion = $strVersion;
    }

    public function __toString()
    {
        return (string)$this->strVersion;
    }

    public function compareVersion($versionToCompare)
    {
        if ($versionToCompare === $this->strVersion) {
            return 0;
        }

        $versionToCompare = $this->evalVersion($versionToCompare);
        $strVersion = $this->evalVersion($this->strVersion);

        for ($i = 0; $i < 4; $i++) {
            if ($strVersion[$i] > $versionToCompare[$i]) {
                return $i + 1;
            }
        }
        return -1;
    }

    private function evalVersion($version)
    {
        return explode('-', $version);
    }
}
