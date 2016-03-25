<?php
namespace AutoUpdate;

class PackageCollection extends Collection
{
    private $factory;

    public function __construct()
    {
        $this->factory = new PackageFactory();
    }

    public function add($version, $address, $file)
    {
        $package = $this->factory->make($version, $address, $file);
        $this->list[$package->name] = $package;
    }
}
