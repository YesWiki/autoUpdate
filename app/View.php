<?php
namespace AutoUpdate;

class View
{
    private $wiki;
    private $views_path = "tools/yeswiki-tool-autoupdate/presentation/views/";

    public function __construct($wiki)
    {
        $this->wiki = $wiki;
    }

    public function show($view = 'status')
    {
        include $this->views_path . "$view.tpl.html";
    }
}
