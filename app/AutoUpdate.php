<?php
namespace AutoUpdate;

class AutoUpdate
{

    private $wiki;

    public function __construct($wiki)
    {
        $this->wiki = $wiki;
    }

    public function isAdmin()
    {
        return $this->wiki->UserIsAdmin();
    }

    public function download()
    {
        $repo = new Repository($this->getRepositoryAddress());
        return $repo->getFile();
    }

    public function extract($file)
    {
        $path = $this->tmpdir();

        $zip = new \ZipArchive;
        if (true !== $zip->open($file)) {
            return false;
        }
        if (true !== $zip->extractTo($path)) {
            return false;
        }
        $zip->close();

        return $path;
    }

    public function upgrade($path)
    {
        return false;
    }

    public function upgradeTools($path)
    {
        return false;
    }

    private function upgradeTool($name, $path)
    {
        return false;
    }

    public function getWikiVersion()
    {
        if (isset($this->wiki->config['wikini_version'])) {
            return $this->wiki->config['wikini_version'];
        }
        return _t('AU_UNKNOW');

    }

    public function isNewVersion()
    {
        $repo = new Repository($this->getRepositoryAddress());

        if ($repo->compareVersion($this->getWikiVersion()) > 0) {
            return true;
        }
        return false;
    }

    private function getRepositoryAddress()
    {
        $repository = null;
        if (isset($this->wiki->config['yeswiki_repository'])) {
            return $this->wiki->config['yeswiki_repository'];
        }
        return $repository;
    }

    private function getWikiDir()
    {
        return dirname(dirname(dirname(__DIR__)));
    }

    private function tmpdir()
    {
        $path = tempnam(sys_get_temp_dir(), 'yeswiki_');

        if (is_file($path)) {
            unlink($path);
        }

        mkdir($path);
        return $path;
    }
}
