<?php
namespace AutoUpload;

class View
{
    protected $twig_loader;
    protected $twig;

    public function __construct()
    {
        $this->twig_loader = new \Twig_Loader_Filesystem(
            'presentation/templates'
        );
        $this->twig = new \Twig_Environment($this->twig_loader);
    }

    public function show($view = 'update_form.twig')
    {
        // TODO : Ajouter ici les valeurs a afficher
        echo $this->twig->render($template, $list_infos);
    }
}
