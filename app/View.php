<?php
namespace AutoUpdate;

class View
{
    private $au;
    private $views_path = "tools/yeswiki-tool-autoupdate/presentation/views/";

    public function __construct($autoupdate)
    {
        $this->au = $autoupdate;
    }

    public function show($view = 'status')
    {
        $messages = new Messages();
        include $this->views_path . "$view.tpl.html";
    }
}
