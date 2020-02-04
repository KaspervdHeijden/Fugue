<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

final class MultiErrorHandler extends ErrorHandler
{
    /** @var ErrorHandlerInterface[] */
    private $handlers;

    public function __construct(ErrorHandlerInterface ...$handlers)
    {
        $this->handlers = $handlers;
    }

    protected function handle(UnhandledErrorException $exception): bool
    {
        foreach ($this->handlers as $handler) {
            $handler->handleError(
                (int)$exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                (int)$exception->getLine()
            );
        }
    }
}
