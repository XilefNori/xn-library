<?php

namespace XnLibrary\Traits;

use XnLibrary\Exception\AssertException;
use XnLibrary\Text;

trait GetterSetterTrait
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $fieldName
     *
     * @return mixed
     * @throws AssertException
     */
    public function get($fieldName)
    {
        $accessor = 'get' . Text::underscoreToCamelCase($fieldName);
        if (method_exists($this, $accessor)) {
            return $this->$accessor();
        }

        throw new AssertException("No getter exists for [$fieldName]");
    }

    /**
     * @param $fieldName
     * @param $value
     *
     * @return mixed
     * @throws AssertException
     */
    public function set($fieldName, $value)
    {
        $mutator = 'set' . Text::underscoreToCamelCase($fieldName);
        if (method_exists($this, $mutator)) {
            return $this->$mutator($value);
        }

        throw new AssertException("No getter exists for [$fieldName]");
    }
}
