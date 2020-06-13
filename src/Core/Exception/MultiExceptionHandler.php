<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;

final class MultiExceptionHandler implements ExceptionHandlerInterface
{
    /** @var ExceptionHandlerInterface[] */
    private array $handlers;

    public function __construct(ExceptionHandlerInterface ...$handlers)
    {
        $this->handlers = $handlers;
    }

    public function handle(Throwable $exception): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($exception);
        }
    }
}
