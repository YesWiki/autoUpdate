<?php
namespace AutoUpdate;

class AutoUpdate
{
    const DEFAULT_REPO = 'http://yeswiki.net/repository/';
    const DEFAULT_VERS = 'Cercopitheque'; // Pour gérer les vielles version de
                                          // YesWiki
    private $wiki;
    private $files = null;
    public $repository = null;

    public function __construct($wiki)
    {
        $this->wiki = $wiki;
        $this->files = new Files();
    }

    public function initRepository()
    {
        $this->repository = new Repository($this->getRepositoryAddress());
        return $this->repository->load();
    }

    public function isAdmin()
    {
        return $this->wiki->UserIsAdmin();
    }

    public function checkFilesACL()
    {
        $path = $this->getWikiDir();
        $files = new Files();
        return $files->isWritable($path);
    }

    public function getYesWikiRelease()
    {
        if (isset($this->wiki->config['yeswiki_release'])) {
            $version = $this->wiki->config['yeswiki_release'];
            if ($this->checkVersionFormat($version)) {
                return $version;
            }
            return "0000-00-00-0";
        }
        return _t('AU_UNKNOW');
    }

    public function isNewVersion()
    {
        $corePackage = $this->repository->getPackage('yeswiki');
        $corePackageVersion = $corePackage->version();

        if ($corePackageVersion->compareVersion($this->getYesWikiRelease()) > 0) {
            return true;
        }
        return false;
    }

    public function getWikiConfiguration()
    {
        return new Configuration($this->getWikiDir() . '/wakka.config.php');
    }

    public function getWikiDir()
    {
        return dirname(dirname(dirname(__DIR__)));
    }

    private function getRepositoryAddress()
    {
        $repositoryAddress = $this::DEFAULT_REPO;

        if (isset($this->wiki->config['yeswiki_repository'])) {
            $repositoryAddress = $this->wiki->config['yeswiki_repository'];
        }

        if (substr($repositoryAddress, -1, 1) !== '/') {
            $repositoryAddress .= '/';
        }

        $repositoryAddress .= $this->getYesWikiVersion();
        return $repositoryAddress;
    }

    private function getYesWikiVersion()
    {
        $version = $this::DEFAULT_VERS;
        if (isset($this->wiki->config['yeswiki_version'])) {
            $version = $this->wiki->config['yeswiki_version'];
        }
        return strtolower($version);
    }

    private function checkVersionFormat($version)
    {
        $pattern = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{1}$/";
        return(preg_match($pattern, $version));
    }
}
