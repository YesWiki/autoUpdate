<?php
namespace AutoUpdate;

abstract class View
{
    private $viewsPath = "tools/yeswiki-tool-autoupdate/presentation/views/";
    private $twig;

    protected $autoUpdate;
    protected $template = "status";

    public function __construct($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
        $twigLoader = new \Twig_Loader_Filesystem($this->viewsPath);
        $this->twig = new \Twig_Environment($twigLoader);
    }

    public function show()
    {
        $infos = $this->grabInformations();
        echo $this->twig->render($this->template . ".tpl.html", $infos);
    }

    abstract protected function grabInformations();
}
