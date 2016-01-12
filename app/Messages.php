<?php
namespace AutoUpdate;

class Messages implements \ArrayAccess, \Iterator, \Countable
{
    private $messages;

    public function __construct()
    {
        $this->messages = array();
    }

    /*************************************************************************
     * Iterator
     ************************************************************************/
    public function rewind()
    {
        return reset($this->messages);
    }
    public function current()
    {
        return current($this->messages);
    }
    public function key()
    {
        return key($this->messages);
    }
    public function valid()
    {
        return isset($this->messages[$this->key()]);
    }
    public function next()
    {
        return next($this->messages);
    }

    /*************************************************************************
     * Countable
     ************************************************************************/
    public function count()
    {
        return count($this->messages);
    }

    /*************************************************************************
     * ArrayAccess
     ************************************************************************/
    public function offsetExists($offset)
    {
        return isset($this->messages[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->messages[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->messages[$offset])
        ? $this->messages[$offset] : null;
    }
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->messages[] = $value;
            return;
        }
        $this->messages[$offset] = $value;
    }

    /*************************************************************************
     * Spécifique
     ************************************************************************/
    public function reset()
    {
        $this->messages = array();
        return $this->messages;
    }

    public function add($message, $status)
    {
        $this[] = array(
            'text' => _t($message),
            'status' => _t($status),
        );
        return $this;
    }
}
