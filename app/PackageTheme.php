<?php
namespace AutoUpdate;

class PackageTheme extends Package
{
    public function upgrade($desPath)
    {
        // TODO
        $desPath = $desPath;
        return false;
    }

    public function updateAvailable()
    {
        return false;
    }

    public function localRelease()
    {
        return new Release("1970-01-01-1");
    }
}
