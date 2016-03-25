<?php
namespace AutoUpdate;

class PackageFactory
{
    public function make($version, $repoUrl, $name)
    {
        $className = $this->getType($name);
        return new $className($version, $repoUrl . $name);
    }

    private function getType($filename)
    {
        switch (explode('-', $filename)[0]) {
            case 'yeswiki':
                return '\AutoUpdate\PackageCore';
                break;

            case 'tool':
                return '\AutoUpdate\PackageTool';
                break;

            case 'theme':
                return '\AutoUpdate\PackageTheme';
                break;

            default:
                throw new \Exception("Type de paquet inconnu", 1);
                break;
        }
    }
}
