<?php
namespace AutoUpdate;

class AutoUpdate
{
    const PARAM_NAME_REPO = 'yeswiki_repository';
    const PARAM_NAME_VERS = 'yeswiki_version';
    const DEFAULT_REPO = 'http://yeswiki.net/repository/';
    const DEFAULT_VERS = 'Cercopitheque'; // Pour gérer les vielles version de
                                          // YesWiki
    private $isUserAdmin;
    public $config;
    public $repository = null;

    public function __construct($config, $isUserAdmin)
    {
        $this->isUserAdmin = $isUserAdmin;
        $this->config = $config;
    }

    public function initRepository()
    {
        $this->repository = new Repository($this->repositoryAddress());
        return $this->repository->load();
    }

    public function isAdmin()
    {
        return $this->isUserAdmin;
    }

    public function baseUrl()
    {
        // TODO Ne pas utiliser $_GET
        if (isset($_GET['wiki'])) {
            return $this->config['base_url'] . $_GET['wiki'];
        }
        // Nous ne devrions jamais arriver ici.
        throw new Exception("baseUrl request in non-wiki context", 1);
    }

    private function repositoryAddress()
    {
        $repositoryAddress = $this::DEFAULT_REPO;

        if (isset($this->config[$this::PARAM_NAME_REPO])) {
            $repositoryAddress = $this->config[$this::PARAM_NAME_REPO];
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
        if (isset($this->config[$this::PARAM_NAME_VERS])) {
            $version = $this->config[$this::PARAM_NAME_VERS];
        }
        return strtolower($version);
    }
}
