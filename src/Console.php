<?php

namespace XnLibrary;

use XnLibrary\Exception\ExecException;
use XnLibrary\Interfaces\LoggableInterface;
use XnLibrary\Traits\GetterSetterTrait;
use XnLibrary\Traits\LoggableTrait;

/**
 * @property bool $dry
 * @property int  $verbose
 */
class Console implements LoggableInterface
{
    use LoggableTrait;
    use GetterSetterTrait;

    const VERBOSE_NONE   = 0;
    const VERBOSE_EXEC   = 1;
    const VERBOSE_INPUT  = 2;
    const VERBOSE_OUTPUT = 4;
    const VERBOSE_ALL    = PHP_INT_MAX;


    /** @var bool */
    protected $_dry = false;

    /** @var int */
    protected $_verbose = self::VERBOSE_ALL;


    public static function create()
    {
        return new self();
    }

    /**
     * @return Console
     */
    public static function getInstance()
    {
        static $_instance;
        if (!$_instance) {
            $_instance = new self();
        }

        return $_instance;
    }

    protected function __construct($logger = null)
    {
        $this->logger = $logger;
    }

    public function callByProcOpen($command, $params = [], $options = [], $input = [])
    {
        return $this->systemByProcOpen($this->makeCmdLine($command, $params, $options), $input);
    }

    public function callByPopen($command, $params = [], $options = [])
    {
        return $this->systemByPopen($this->makeCmdLine($command, $params, $options));
    }

    public function makeCmdLine($command, $params = [], $options = [])
    {
        $cmd_opts = $this->makeOpts((array) $options);
        $cmd_opts = array_merge($cmd_opts, (array) $params);
        $cmd_line = $this->makeCmd($command, $cmd_opts);

        return $cmd_line;
    }

    public function makeCmd($command, $cmd_opts)
    {
        $cmd_opts = $this->filterArgs($cmd_opts);
        $cmd      = $command . ' ' . implode(' ', $cmd_opts);

        return $cmd;
    }

    public function filterArgs($args)
    {
        return array_map('escapeshellarg', (array) $args);
    }

    public function makeOpts($opts)
    {
        $cmd_opts = [];

        foreach ($opts as $opt => $val) {
            if (!is_string($opt)) {
                $opt = $val;
                $val = null;
            }

            if (is_bool($val)) {
                if ($val) {
                    $val = null;
                } else {
                    continue;
                }
            }

            $cmd_opts[] = $opt;
            if ($val !== null) {
                $cmd_opts[] = $val;
            }
        }

        return $cmd_opts;
    }

    public function systemByPopen($cmd, &$ret = null)
    {
        $this->logExec('exec', $cmd);

        $lines = [];
        if (!$this->_dry) {
            $handle = popen($cmd, 'r');
            while (($line = fgets($handle)) !== false) {
                $line    = trim($line);
                $lines[] = $line;

                $this->logExec('output', $line);
            }

            $ret = pclose($handle);
        } else {
            $ret = 0;
        }

        if ($ret) {
            if ($ret == -1) {
                throw new ExecException("Unable to run command: $cmd", $ret);
            }

            throw new ExecException("Error running command: $cmd", $ret);
        }

        return $lines;
    }

    public function systemByProcOpen($cmd, $input = [], Console\Accepter $accepter = null)
    {
        if ($this->_dry) {
            $this->logExec('exec', '--- !!! DRY MODE !!! ---');
        }

        $this->logExec('exec', $cmd);

        $descriptors = [
            0 => ["pipe", "r"],  // stdin is a pipe that the child will read from
            1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
            2 => ["pipe", "w"],  // stderr is a pipe that the child will write to
        ];

        foreach ($input as $item) {
            $this->logExec('input', $item);
        }

        $lines = [];
        if (!$this->_dry) {
            $handle = proc_open($cmd, $descriptors, $pipes);

            foreach ($input as $item) {
                fwrite($pipes[0], $item . "\n");
            }
            fclose($pipes[0]);
            array_shift($pipes);

            while (($line = fgets($pipes[0])) !== false) {
                $line = trim($line);
                if ($accepter && !$accepter->accept($line)) {
                    continue;
                }

                $this->logExec('output', $line);
                $lines[] = $line;
            }

            foreach ($pipes as $p) {
                fclose($p);
            }
            $ret = proc_close($handle);
        } else {
            $ret = 0;
        }

        if ($ret) {
            if ($ret == -1) {
                throw new ExecException("Unable to run command: $cmd", $ret);
            }

            throw new ExecException("Error running command: $cmd", $ret);
        }

        return $lines;
    }

    public function system($cmd, &$ret = null, $throw = true)
    {
        $this->logExec('exec', $cmd);

        $last_line = '';
        if (!$this->_dry) {
            $last_line = system($cmd, $ret);
        }

        if ($throw && $ret) {
            if ($ret == -1) {
                throw new ExecException("Unable to run command: $cmd", $ret);
            }

            throw new ExecException("Error running command: $cmd", $ret);
        }

        return $last_line;
    }

    public function execFmt($format, array $args = [], &$ret = null, $throw = true)
    {
        $args = $this->filterArgs($args);
        $cmd  = sprintf(...array_merge([$format], $args));

        return $this->exec($cmd, $ret, $throw);
    }

    public function exec($cmd, &$ret = null, $throw = true)
    {
        $this->logExec('exec', $cmd);

        $lastline = '';
        if (!$this->_dry) {
            $lastline = exec($cmd, $output, $ret);

            foreach ($output as $line) {
                $this->logExec('output', $line);
            }
        }

        if ($throw && $ret) {
            if ($ret == -1) {
                throw new ExecException("Unable to run command: $cmd", $ret);
            }

            throw new ExecException("Error running command: $cmd", $ret);
        }

        return $lastline;
    }

    // Getters & setters

    public function getDry()
    {
        return $this->_dry;
    }

    public function setDry($dry)
    {
        $this->_dry = $dry;

        return $this;
    }

    public function getVerbose()
    {
        return $this->_verbose;
    }

    public function setVerbose($verbose)
    {
        $this->_verbose = $verbose;

        return $this;
    }


    protected function logExec($type, $message)
    {
        if ($this->_verbose && $this->getVerboseBit($type)) {
            $this->logInfo("$type: [$message]");
        }
    }

    protected function getVerboseBit($type)
    {
        static $values;

        if (!isset($values[$type])) {
            $values[$type] = constant('self::VERBOSE_' . strtoupper($type));
        }

        return $values[$type];
    }
}
