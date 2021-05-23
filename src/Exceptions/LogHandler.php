<?php

declare(strict_types=1);

namespace Chinstrap\Core\Exceptions;

use Chinstrap\Core\Contracts\Exceptions\Handler;
use Psr\Log\LoggerInterface;
use Throwable;
use Whoops\Exception\Inspector;
use Whoops\RunInterface;

final class LogHandler implements Handler
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Throwable $exception, Inspector $inspector, RunInterface $run): void
    {
        $this->logger->error($exception->getMessage(), $exception->getTrace());
    }
}
