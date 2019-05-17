<?php

namespace XnLibrary\Console;

abstract class Accepter
{
    /**
     * @param $line
     *
     * @return mixed
     */
    abstract public function accept($line);
}
