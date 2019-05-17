<?php

namespace XnLibrary\Exception;

use XnLibrary\ExceptionHelper;

class BaseException extends \Exception
{
    protected $input = null;

    public function hasInput()
    {
        return !is_null($this->input);
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setInput($input)
    {
        $this->input = $input;
    }


    public function info($show = 'fp')
    {
        return ExceptionHelper::info($this, $show);
    }
}
