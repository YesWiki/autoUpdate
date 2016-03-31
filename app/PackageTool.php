<?php
namespace AutoUpdate;

class PackageTool extends PackageExt
{
    const TOOL_PATH = 'tools/';

    public function upgrade($desPath)
    {
        // TODO
        $desPath = $desPath;
        return false;
    }

    protected function localPath()
    {
        return $this::TOOL_PATH . $this->name() . '/';
    }
}
