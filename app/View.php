<?php
namespace AutoUpdate;

class View
{
    protected $twig_loader;
    protected $twig;

    public function __construct()
    {
        $this->twig_loader = new \Twig_Loader_Filesystem(
            'presentation/templates/'
        );
        $this->twig = new \Twig_Environment($this->twig_loader);
    }

    public function show($view = 'update_form.twig')
    {
        // TODO : Ajouter ici les valeurs a afficher
        $view = 'auth.twig';
        echo $this->twig->render($view, array());
    }
}
