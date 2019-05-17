<?php

namespace XnLibrary\Console\Accepter;

use XnLibrary\Console\Accepter;

class AfterLine extends Accepter
{
    protected $accept;
    protected $delimiter;

    public function __construct($delimiter)
    {
        $this->accept    = false;
        $this->delimiter = $delimiter;
    }

    public function accept($line)
    {
        if ($this->accept) {
            return true;
        }

        if ($line == $this->delimiter) {
            $this->accept = true;
        }

        return false;
    }
}
