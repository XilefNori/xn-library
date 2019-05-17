<?php

namespace XnLibrary;

use Psr\Log\LoggerInterface;
use XnLibrary\Exception\AssertException;
use XnLibrary\Interfaces\LoggableInterface;
use XnLibrary\Traits\LoggableTrait;

class Cmd implements LoggableInterface
{
    use LoggableTrait;

    protected $_console;

    public static function create()
    {
        return new self();
    }

    public static function getInstance()
    {
        static $_instance = null;

        if (!$_instance) {
            $_instance = new self();
        }

        return $_instance;
    }

    public function setLogger(?LoggerInterface $logger)
    {
        $this->console()->setLogger($logger);

        return $this;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->console()->getLogger();
    }

    public function setConsole(Console $console)
    {
        $this->_console = $console;

        return $this;
    }

    public function console()
    {
        if (!$this->_console) {
            $this->_console = Console::create();
        }

        return $this->_console;
    }

    public function makeCmdLine($command, $params = [], $options = [])
    {
        return $this->console()->makeCmdLine($command, $params, $options);
    }

    public function which($bin)
    {
        return $this->console()->callByPopen('which', [$bin])[0];
    }

    public function file($filename, $options = [])
    {
        if (!file_exists($filename)) {
            throw new AssertException(sprintf('Filename not exists [%s]!', $filename));
        }

        $options['--brief'] = true;

        list($line) = $this->console()->callByPopen('file', [$filename], $options);

        return $line;
    }

    public function mimetype($filename)
    {
        if (!file_exists($filename)) {
            throw new AssertException(sprintf('Filename [%s] not exists!', $filename));
        }

        $options['--brief']     = true;
        $options['--mime-type'] = true;

        list($line) = $this->console()->callByPopen('file', [$filename], $options);

        return $line;
    }

    public function ssh($host, $options, $cmds)
    {
        $delimiter = 'CUT-BEFORE-THIS-LINE';
        array_unshift($cmds, "echo '$delimiter'");

        $line     = $this->makeCmdLine('ssh', [$host], $options);
        $accepter = new Console\Accepter\AfterLine($delimiter);

        return $this->console()->systemByProcOpen($line, $cmds, $accepter);
    }
}
