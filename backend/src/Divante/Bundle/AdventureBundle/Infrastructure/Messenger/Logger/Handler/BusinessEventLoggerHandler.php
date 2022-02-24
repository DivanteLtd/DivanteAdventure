<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Messenger\Logger\Handler;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;
use Symfony\Bridge\Monolog\Processor\TokenProcessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BusinessEventLoggerHandler extends RotatingFileHandler
{

    public function __construct(
        string $filename,
        TokenStorageInterface $tokenStorage,
        int $maxFiles = 10,
        int $level = Logger::DEBUG,
        bool $bubble = true,
        ?int $filePermission = null,
        bool $useLocking = false
    ) {
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
        $this->setFormatter(new LogstashFormatter('business_event_logger'));
        $this->pushProcessor(new WebProcessor());
        $this->pushProcessor(new TokenProcessor($tokenStorage));
    }
}
