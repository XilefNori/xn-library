<?php

namespace XnLibrary;

use Countable;

class XnCollection extends XnObject implements Countable
{
    /**
     * @param callback $callback
     *
     * @return XnCollection
     */
    public function sort($callback): XnCollection
    {
        uasort($this->data, $callback);

        return $this;
    }

    /* Converters */

    public function toJsonArray(array $options = []): array
    {
        $data = array();
        /** @var XnObject $obj */
        foreach ($this as $obj) {
            $data[] = $obj->toJsonArray($options);
        }

        return $data;
    }

    public function toKeyValueArray($key, $value)
    {
        $result = array();
        foreach ($this as $record) {
            $result[$record->$key] = $record->$value;
        }

        return $result;
    }

    public function remove($key)
    {
        $removed = $this->data[$key];

        unset($this->data[$key]);

        return $removed;
    }

    public function contains($key)
    {
        return isset($this->data[$key]);
    }

    public function search($rule)
    {
        return array_search($rule, $this->data, true);
    }

    /**
     * @param callable $callback
     * @param int      $flag
     *
     * @return XnCollection
     * @see array_filter
     */
    public function filter($callback, $flag = 0)
    {
        return new self(array_filter($this->data, $callback, $flag));
    }

    public function count()
    {
        return count($this->data);
    }

    /**
     * @param XnObject $object
     * @param bool     $by_id
     *
     * @return bool
     */
    public function add($object, $by_id = false)
    {
        if ($by_id) {
            $this->data[$object->id] = $object;
        } else {
            $this->data[] = $object;
        }

        return true;
    }

    /**
     * @return XnObject|null
     */
    public function pop()
    {
        return array_pop($this->data);
    }

    /**
     * @return XnObject|null
     */
    public function shift()
    {
        return array_shift($this->data);
    }

    /**
     * @param XnCollection $coll
     *
     * @return XnCollection
     */
    public function merge($coll, $by_id = false)
    {
        /** @var XnObject $rule */
        foreach ($coll as $rule) {
            $this->add($rule, $by_id);
        }

        // $this->data = array_merge($this->data, $coll->getData());

        return $this;
    }

    public function clear()
    {
        $this->data = array();
    }

    public function getFirst()
    {
        return reset($this->data);
    }

    public function getLast()
    {
        return end($this->data);
    }

    public function end()
    {
        return end($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

}
