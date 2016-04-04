<?php
namespace AutoUpdate;

class Configuration extends Collection
{
    private $file = null;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function load()
    {
        include $this->file;
        if (isset($wakkaConfig)) {
            $this->list = $wakkaConfig;
        }
    }

    public function write($file = null, $arrayName = "wakkaConfig")
    {
        if (is_null($file)) {
            $file = $this->file;
        }

        //TODO gérer les sous tableaux (utiliser var_export)
        $content = "<?php\n" . "\$$arrayName = array(\n";
        foreach ($this->list as $key => $value) {
            $content .= "    \"" . $key . "\" => \"" . $value . "\",\n";
        }
        $content .= ");\n";
        if (file_put_contents($file, $content) === false) {
            return false;
        }
        return true;
    }
}
