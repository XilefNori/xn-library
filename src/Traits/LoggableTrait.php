<?php

namespace XnLibrary\Traits;

use Psr\Log\LoggerInterface;

trait LoggableTrait
{
    /** @var LoggerInterface|null */
    protected $logger;


    public function setLogger(?LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }


    // Logging messages

    public function logCommon($message, $priority, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->log($message, $priority, $extras);
        }
    }

    public function logInfo($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->info($message, $extras);
        }
    }

    public function logAlert($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->alert($message, $extras);
        }
    }

    public function logCritical($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->critical($message, $extras);
        }
    }

    public function logDebug($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->debug($message, $extras);
        }
    }

    public function logEmergency($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->emergency($message, $extras);
        }
    }

    public function logError($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->error($message, $extras);
        }
    }

    public function logNotice($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->notice($message, $extras);
        }
    }

    public function logWarning($message, $extras = null)
    {
        if ($logger = $this->getLogger()) {
            $logger->warning($message, $extras);
        }
    }
}

