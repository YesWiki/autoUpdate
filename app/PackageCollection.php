<?php
namespace AutoUpdate;

class PackageCollection extends Collection
{
    const THEME_CLASS = 'AutoUpdate\PackageTheme';
    const TOOL_CLASS = 'AutoUpdate\PackageTool';
    const CORE_CLASS = 'AutoUpdate\PackageCore';

    public function add($version, $address, $file)
    {
        $className = $this->getPackageType($file);
        $package = new $className($version, $address . $file);
        $this->list[$package->name] = $package;
    }

    public function getThemesPackages()
    {
        return $this->filterPackages($this::THEME_CLASS);
    }

    public function getToolsPackages()
    {
        return $this->filterPackages($this::TOOL_CLASS);
    }

    private function filterPackages($class)
    {
        $filteredPackages = new PackageCollection();
        foreach ($this->list as $package) {
            if (get_class($package) === $class) {
                $filteredPackages[] = $package;
            }
        }
        return $filteredPackages;
    }

    private function getPackageType($filename)
    {
        $type = explode('-', $filename)[0];
        switch ($type) {
            case 'yeswiki':
                return $this::CORE_CLASS;
                break;

            case 'tool':
                return $this::TOOL_CLASS;
                break;

            case 'theme':
                return $this::THEME_CLASS;
                break;

            default:
                throw new \Exception("Type de paquet inconnu", 1);
                break;
        }
    }
}
