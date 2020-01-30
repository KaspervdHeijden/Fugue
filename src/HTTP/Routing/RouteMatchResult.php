<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

final class RouteMatchResult
{
    /** @var bool */
    private $matches;

    /** @var string[] */
    private $arguments;

    public function __construct(bool $matches, array $arguments)
    {
        $this->matches = $matches;
        $this->arguments = $arguments;
    }

    /**
     * @return bool
     */
    public function matches(): bool
    {
        return $this->matches;
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
