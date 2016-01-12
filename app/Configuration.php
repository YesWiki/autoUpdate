<?php
namespace AutoUpdate;

class Configuration implements \ArrayAccess
{
    /**
     *
     * @var array
     */
    private $config;
    private $file;

    /**
     * [__construct description]
     * @param [type] $file [description]
     */
    public function __construct($file)
    {
        $this->file = $file;
        include $file;
        if (isset($wakkaConfig)) {
            $this->config = $wakkaConfig;
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->config[] = $value;
            return;
        }
        $this->config[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }

    /**
     * écris le fichier de configuration
     * @return [type] [description]
     */
    public function write($file = null, $arrayName = "wakkaConfig")
    {
        if (is_null($file)) {
            $file = $this->file;
        }

        $content = "<?php\n";
        $content .= "\$$arrayName = array(\n";
        foreach ($this->config as $key => $value) {
            $content .= "    \"" . $key . "\" => \"" . $value . "\",\n";
        }
        $content .= ");\n";
        if (file_put_contents($file, $content) === false) {
            return false;
        }
        return true;
    }
}
