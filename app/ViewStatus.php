<?php
namespace AutoUpdate;

class ViewStatus extends View
{
    public function __construct($autoUpdate)
    {
        parent::__construct($autoUpdate);
        $this->template = "status";
    }

    protected function grabInformations()
    {
        $infos = array(
            'wikiVersion' => $this->autoUpdate->getYesWikiRelease(),
            'repoVersion' => $this->autoUpdate->repository->getVersion(),
            'link' => "?wiki=" . $_GET['wiki'] . "&autoupdate=upgrade",
            'isAdmin' => $this->autoUpdate->isAdmin(),
            'isNewVersion' => $this->autoUpdate->isNewVersion(),
            'AU_UPDATE' => _t('AU_UPDATE'),
            'AU_FORCE_UPDATE' => _t('AU_FORCE_UPDATE'),
            'AU_WARNING' => _t('AU_WARNING'),
            'AU_VERSION_REPO' => _t('AU_VERSION_REPO'),
            'AU_VERSION_WIKI' => _t('AU_VERSION_WIKI'),
        );
        return $infos;
    }
}
