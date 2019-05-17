<?php

namespace XnLibrary\Traits;

use XnLibrary\Json;

trait ArrayAccessTrait
{
    protected $data;

    // ArrayAccess

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;

        return $this;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }


    public function getKeys()
    {
        return array_keys($this->data);
    }

    // IteratorAggregate

    public function getIterator()
    {


        return new \ArrayIterator($this->data);
    }


    // Data manipulation

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFieldsJson(array $props = array(), $utf8 = true, $indent = false, $unescape_slashes = false)
    {
        return Json::format($this->getFields($props), $utf8, $indent, $unescape_slashes);
    }

    public function getFields(array $props = [])
    {
        if (!$props) {
            return $this->getData();
        }

        $list = [];
        foreach ($props as $key) {
            $list[$key] = $this->data[$key];
        }

        return $list;
    }

    public function updateFields($data, $keys = null)
    {
        if ($keys) {
            foreach ((array) $keys as $key) {
                $this->data[$key] = $data[$key];
            }
        } else {
            $this->data = array_merge($this->data, $data);
        }
    }
}
