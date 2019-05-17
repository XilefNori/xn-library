<?php

namespace XnLibrary\Interfaces;

use Psr\Log\LoggerInterface;

interface LoggableInterface
{
    public function logCommon($message, $priority, $context = null);

    public function logInfo($message, $context = null);
    public function logAlert($message, $context = null);
    public function logDebug($message, $context = null);
    public function logError($message, $context = null);
    public function logNotice($message, $context = null);
    public function logWarning($message, $context = null);

    public function getLogger(): ?LoggerInterface;
    public function setLogger(?LoggerInterface $log);
}
