<?php
namespace AutoUpdate;

class ApiController
{
    /**
     *
     * @var AutoUpdate
     */
    public $autoUpdate;

    /**
     * Constructor
     * @param AutoUpdate $autoUpdate [description]
     */
    public function __construct($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
    }

    public function run($method, $route)
    {
        if (!$this->autoUpdate->initRepository()) {
            // Internal server error
            (new ApiResponse(500))->send();
            return;
        }

        if(empty($route))
        {
            // Bad request
            (new ApiResponse(400))->send();
            return;
        }

        $method = strtolower($method);
        $method .= ucfirst($route->ressource);

        if (!$this->isValidMethod($method)) {
            // Bad request
            (new ApiResponse(400))->send();
            return;
        }

        $this->$method($route->identity);
    }

    private function getPackages($name)
    {
        $data = array();
        if ($name === "") {
            $packages = $this->autoUpdate->repository->getPackages();
            foreach ($packages as $package) {
                $data[$package->name] = $this->extractPackageInfos($package);
            }
            (new ApiResponse(200, $data))->send();
            return $data;
        }

        if ($package = $this->autoUpdate->repository->getPackage($name)) {
            $data[$package->name] = $this->extractPackageInfos($package);
            (new ApiResponse(200, $data))->send();
            return $data;
        }

        (new ApiResponse(400))->send();
    }

    private function postPackages($name)
    {
        (new ApiResponse(501))->send();
    }

    private function putPackages($name)
    {
        (new ApiResponse(501))->send();
    }

    private function deletePackages($name)
    {
        (new ApiResponse(501))->send();
    }

    private function isValidMethod($method)
    {
        return in_array($method, get_class_methods($this));
    }

    private function extractPackageInfos($package)
    {
        return array(
            "name" => $package->name,
            "installed" => $package->installed,
            "release" => (string)$package->release,
            "upgrade" => $package->updateAvailable,
            "localRelease" => (string)$package->localRelease,
            "description" => $package->description,
            "documentation" => $package->documentation,
        );
    }
}
