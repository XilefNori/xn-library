<?php

namespace XnLibrary\Tests;

include_once __DIR__ . '/bootstrap.php';

use Monolog\Handler\TestHandler;
use Monolog\Logger;

class TestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var TestHandler
     */
    protected $handler;


    public function setUp()
    {
        $this->handler = new TestHandler();
        $this->logger  = new Logger('main');
        $this->logger->pushHandler($this->handler);
    }
}
