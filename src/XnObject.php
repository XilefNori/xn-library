<?php

namespace XnLibrary;

use XnLibrary\Interfaces\JsonableInterface;
use XnLibrary\Interfaces\LoggableInterface;
use XnLibrary\Traits\ArrayObjectTrait;
use XnLibrary\Traits\LoggableTrait;

class XnObject implements \ArrayAccess, \IteratorAggregate, JsonableInterface, LoggableInterface
{
    use LoggableTrait;
    use ArrayObjectTrait {
        ArrayObjectTrait::updateData as traitUpdateData;
        ArrayObjectTrait::setData as traitSetData;
    }

    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * Get cached attribute or if missed run callback to evaluate
     *
     * @param string   $key
     * @param callable $callback
     *
     * @return mixed
     */
    protected function _getCached($key, $callback)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $callback();
        }

        return $this->data[$key];
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->traitSetData($data);
        $this->_build();

        return $this;
    }

    /**
     *
     */
    protected function _build()
    {

    }

    /**
     * @param array        $data
     * @param array|string $keys
     *
     * @return $this
     */
    public function updateData(array $data, $keys = null)
    {
        return $this->traitUpdateData($data, $keys);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function toJsonArray(array $options = []): array
    {
        return $this->toArray();
    }

    /**
     * @param string $name
     * @param array  $options
     * @param bool   $utf8
     * @param bool   $indent
     * @param bool   $escape_slashes
     *
     * @return string
     */
    public function toJsonString($name, $options, $utf8 = true, $indent = true, $escape_slashes = true)
    {
        return Json::format($this->toJsonArray($options), $utf8, $indent, $escape_slashes);
    }

    protected function _getField($key, $to_array = true)
    {
        if (!strpos($key, '.')) {
            return $this[$key];
        }

        $names = explode('.', $key);

        $field  = array_pop($names);
        $object = $this;
        foreach ($names as $n) {
            $keys[] = $n;
            !isset($object[$n]) and $n = ucfirst($n);

            if (isset($object[$n])) {
                $object = $object[$n];
            } else {
                return sprintf('<%s-unset!>', implode('.', $keys));
            }
        }

        $keys[] = $field;
        $value  = $object[$field] ?? sprintf('<%s-unset!>', implode('.', $keys));

        if ($to_array && $value instanceof self) {
            $value = $value->toArray();
        }

        return $value;
    }

    /**
     * @param array $keys
     * @param bool  $to_array
     *
     * @return array
     */
    public function getFields(array $keys = [], $to_array = true)
    {
        if (!$keys) {
            $keys = $this->_getKeys();
        }

        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $this->_getField($key, $to_array);
        }

        return $data;
    }

    public function toArray($keys = null, $except_keys = null)
    {
        if (!$keys) {
            $keys = $this->_getKeys();
        }

        if ($except_keys) {
            $keys = array_diff($keys, $except_keys);
        }

        $keys = (array) $keys;
        $data = [];
        foreach ($keys as $key) {
            $value = $this->get($key);

            if (is_object($value)) {
                if (is_callable([$value, 'toArray'])) {
                    $value = call_user_func([$value, 'toArray']);
                } else {
                    $value = (string) $value;
                }
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * @param string|array $names
     *
     * @return array
     */
    public function getJsonNames($names)
    {
        if (!is_array($names)) {
            $names = explode('/', strtr($names, ['~' => '/', '+' => '/']));
        }

        return $names;
    }
}
