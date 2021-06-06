<?php

declare(strict_types=1);

namespace Fugue\Core\Output;

use function array_search;
use function is_int;

final class MultiOutputHandler implements OutputHandlerInterface
{
    private array $outputHandlers;

    public function __construct(OutputHandlerInterface ...$outputHandlers)
    {
        $this->outputHandlers = $outputHandlers;
    }

    private function getHandlerIndex(OutputHandlerInterface $handler): ?int
    {
        $index = array_search($handler, $this->outputHandlers, true);
        if (is_int($index)) {
            return (int)$index;
        }

        return null;
    }

    public function add(OutputHandlerInterface $handler): void
    {
        if ($this->getHandlerIndex($handler) === null) {
            $this->outputHandlers[] = $handler;
        }
    }

    public function remove(OutputHandlerInterface $handler): void
    {
        $index = $this->getHandlerIndex($handler);
        if (is_int($index)) {
            unset($this->outputHandlers[$index]);
        }
    }

    public function getHandlers(): array
    {
        return $this->getHandlers();
    }

    public function write(string $text): void
    {
        foreach ($this->outputHandlers as $outputHandler) {
            $outputHandler->write($text);
        }
    }
}
