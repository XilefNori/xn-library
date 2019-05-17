<?php

namespace XnLibrary\Traits;

use XnLibrary\Text;

trait ArrayObjectTrait
{
    protected $data = [];

    // ArrayAccess

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function _getKeys()
    {
        return array_keys($this->data);
    }

    // IteratorAggregate

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }


    // Data

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData($keys = null)
    {
        if (!$keys) {
            return $this->data;
        }

        $list = [];
        foreach ((array) $keys as $key) {
            $list[$key] = $this->data[$key];
        }

        return $list;
    }

    public function updateData($data, $keys = null)
    {
        if ($keys) {
            foreach ((array) $keys as $key) {
                $this->data[$key] = $data[$key];
            }
        } else {
            $this->data = array_merge($this->data, $data);
        }

        return $this;
    }

    public function getDataJson(array $keys = array(), $utf8 = true, $indent = false, $unescape_slashes = false)
    {
        return Json::format($this->getData($keys), $utf8, $indent, $unescape_slashes);
    }

    // ObjectAccess

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    protected function _accessor($fieldName)
    {
        $accessor = 'get' . Text::underscoreToCamelCase($fieldName);
        if (method_exists($this, $accessor)) {
            return $accessor;
        }

        return null;
    }

    protected function _mutator($fieldName)
    {
        $accessor = 'set' . Text::underscoreToCamelCase($fieldName);
        if (method_exists($this, $accessor)) {
            return $accessor;
        }

        return null;
    }

    public function fieldExists($fieldName, $onGet)
    {
        return $this->offsetExists($fieldName) || (
            $onGet ? $this->_accessor($fieldName) : $this->_mutator($fieldName)
            );
    }

    public function get($fieldName)
    {
        if ($fieldName && $accessor = $this->_accessor($fieldName)) {
            return $this->$accessor();
        }

        return $this->_get($fieldName);
    }

    /**
     * @param $fieldName
     * @param $value
     *
     * @return $this
     */
    public function set($fieldName, $value)
    {
        if ($fieldName && $mutator = $this->_mutator($fieldName)) {
            return $this->$mutator($value);
        }

        return $this->_set($fieldName, $value);
    }

    public function toArray($keys = null)
    {
        if (!$keys) {
            $keys = $this->_getKeys();
        }

        $data = [];
        foreach ((array) $keys as $key) {
            $data[$key] = $this->get($key);
        }

        return $data;
    }

    public function fromArray($data, array $keys = [])
    {
        if ($keys) {
            foreach ($keys as $key) {
                $this->set($key, $data[$key]);
            }
        } else {
            foreach ($data as $key => $value) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    public function toArrayJson(array $keys = array(), $utf8 = true, $indent = false, $unescape_slashes = false)
    {
        return Json::format($this->toArray($keys), $utf8, $indent, $unescape_slashes);
    }

    protected function _get($fieldName)
    {
        return $this->data[$fieldName] ?? null;
    }

    protected function _set($fieldName, $value)
    {
        if ($fieldName) {
            $this->data[$fieldName] = $value;
        } else {
            $this->data[] = $value;
        }

        return $this;
    }
}
