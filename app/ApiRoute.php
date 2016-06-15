<?php
namespace AutoUpdate;

class ApiRoute
{

    public $ressource;
    public $identity;

    public function __construct($route)
    {
        $this->analyseRoute($route);
    }

    private function analyseRoute($route)
    {
        if (empty($route)) {
            $this->ressource = null;
            $this->identity = null;
            return;
        }

        $chunks = explode('/', $route);

        $this->ressource = $chunks[0];
        $this->identity = "";
        if (isset($chunks[1])) {
            $this->identity = $chunks[1];
        }
    }
}
