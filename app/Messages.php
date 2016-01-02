<?php
namespace AutoUpdate;

class Messages implements \ArrayAccess, \Iterator, \Countable
{

    /*************************************************************************
     * Iterator
     ************************************************************************/
    public function rewind()
    {
        return reset($_SESSION['messages']);
    }
    public function current()
    {
        return current($_SESSION['messages']);
    }
    public function key()
    {
        return key($_SESSION['messages']);
    }
    public function valid()
    {
        return isset($_SESSION['messages'][$this->key()]);
    }
    public function next()
    {
        return next($_SESSION['messages']);
    }

    /*************************************************************************
     * Countable
     ************************************************************************/
    public function count()
    {
        return count($_SESSION['messages']);
    }

    /*************************************************************************
     * ArrayAccess
     ************************************************************************/
    public function offsetExists($offset)
    {
        return isset($_SESSION['messages'][$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($_SESSION['messages'][$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($_SESSION['messages'][$offset])
        ? $_SESSION['messages'][$offset] : null;
    }
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $_SESSION['messages'][] = $value;
            return;
        }
        $_SESSION['messages'][$offset] = $value;
    }

    /*************************************************************************
     * Spécifique
     ************************************************************************/
    public function reset()
    {
        $_SESSION['messages'] = array();
        return $_SESSION['messages'];
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
