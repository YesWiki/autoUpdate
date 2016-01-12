<?php
namespace AutoUpdate;

class View
{
    private $autoUpdate;
    private $viewsPath = "tools/yeswiki-tool-autoupdate/presentation/views/";
    protected $twig;

    public function __construct($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
        $twigLoader = new \Twig_Loader_Filesystem($this->viewsPath);
        $this->twig = new \Twig_Environment($twigLoader);
    }

    public function show($view = 'status')
    {
        $infos = $this->grabInformations();
        echo $this->twig->render("$view.tpl.html", $infos);
    }

    private function grabInformations()
    {
        $infos = array();
        $infos['wikiVersion'] = $this->autoUpdate->getWikiVersion();
        $infos['repoVersion'] = $this->autoUpdate->getRepoVersion();
        $infos['link']  = "?wiki=" . $_GET['wiki'] . "&autoupdate=upgrade";
        $infos['isAdmin'] = $this->autoUpdate->isAdmin();
        $infos['isNewVersion'] = $this->autoUpdate->isNewVersion();
        $infos['AU_UPDATE'] = _t('AU_UPDATE');
        $infos['AU_FORCE_UPDATE'] = _t('AU_FORCE_UPDATE');
        $infos['AU_WARNING'] = _t('AU_WARNING');
        $infos['AU_VERSION_REPO'] = _t('AU_VERSION_REPO');
        $infos['AU_VERSION_WIKI'] = _t('AU_VERSION_WIKI');

        $infos['messages'] = new Messages();
        return $infos;
    }
}
