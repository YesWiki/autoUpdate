<?php
namespace AutoUpdate;

class View
{
    private $autoupload;
    private $viewsPath = "tools/yeswiki-tool-autoupdate/presentation/views/";

    public function __construct($autoupdate)
    {
        $this->autoupload = $autoupdate;
    }

    public function show($view = 'status')
    {
        $messages = new Messages();
        include($this->viewsPath . "$view.tpl.html");
    }
}
