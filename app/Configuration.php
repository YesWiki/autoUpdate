<?php
namespace AutoUpdate;

class Configuration extends Collection
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
        include $file;
        if (isset($wakkaConfig)) {
            $this->list = $wakkaConfig;
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->list[] = $value;
            return;
        }
        $this->list[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->list[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }
 
    public function write($file = null, $arrayName = "wakkaConfig")
    {
        if (is_null($file)) {
            $file = $this->file;
        }

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
