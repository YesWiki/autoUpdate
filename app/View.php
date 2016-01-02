<?php
namespace AutoUpdate;

class View
{
    private $autoUpdate;
    private $viewsPath = "tools/yeswiki-tool-autoupdate/presentation/views/";

    public function __construct($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
    }

    public function show($view = 'status')
    {
        $messages = new Messages();
        include($this->viewsPath . "$view.tpl.html");
    }
}
