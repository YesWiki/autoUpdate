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

    /**
     * [run description]
     * @param  [type] $method [description]
     * @param  [type] $route  [description]
     * @return [type]         [description]
     */
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

    /**
     * Send back package's informations
     * @param  string $name package's name or empty string for all.
     * @return array       Informations about packages.
     */
    private function getPackages($name = "")
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

    /**
     * Install package if possible.
     * @param  string $name package's name
     * @return [type]       [description]
     */
    private function postPackages($name)
    {
        // Package don't exist.
        if (!$package = $this->autoUpdate->repository->getPackage($name)) {
            (new ApiResponse(404))->send();
            return;
        }
        // Already installed
        if ($package->installed) {
            (new ApiResponse(406))->send();
            return;
        }

        // Téléchargement de l'archive
        $file = $package->getFile();
        if (false === $file) {
            (new ApiResponse(500))->send();
            return;
        }

        // Vérification MD5
        if (!$package->checkIntegrity($file)) {
            (new ApiResponse(500))->send();
            return;
        }

        // Extraction de l'archive
        $path = $package->extract();
        if (false === $path) {
            (new ApiResponse(500))->send();
            return;
        }

        // Vérification des droits sur le fichiers
        if (!$package->checkACL()) {
            (new ApiResponse(500))->send();
            return;
        }

        // Mise à jour du paquet
        if (!$package->upgrade()) {
            (new ApiResponse(500))->send();
            return;
        }

        if (get_class($package) === PackageCollection::CORE_CLASS) {
            // Mise à jour des tools.
            if (!$package->upgradeTools()) {
                (new ApiResponse(500))->send();
                return;
            }
        }

        // Mise à jour de la configuration de YesWiki
        if (!$package->upgradeInfos()) {
            (new ApiResponse(500))->send();
            return;
        }

        (new ApiResponse(200))->send();
    }

    /**
     * Update package or reinstall (only if already installed)
     * @param  string $name package's name, empty for all (only if installed)
     * @return [type]       [description]
     */
    private function putPackages($name = "")
    {
        $this->deletePackages($name);
        $this->postPackages($name);
    }

    private function deletePackages($name)
    {
        // Package don't exist.
        if (!$package = $this->autoUpdate->repository->getPackage($name)) {
            (new ApiResponse(404))->send();
            return;
        }
        // Not installed
        if (!$package->installed) {
            (new ApiResponse(406))->send();
            return;
        }

        if (false === $package->deletePackage()) {
            (new ApiResponse(500))->send();
            return;
        }

        (new ApiResponse(200))->send();
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
