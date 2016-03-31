<?php
namespace AutoUpdate;

class PackageTheme extends PackageExt
{
    const THEME_PATH = 'themes/';

    public function upgrade($desPath)
    {
        // TODO
        $desPath = $desPath;
        return false;
    }

    protected function localPath()
    {
        return $this::THEME_PATH . $this->name() . '/';
    }
}
