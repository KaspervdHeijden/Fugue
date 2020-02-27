<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

final class RouteMatchResult
{
    /** @var string[] */
    private $arguments;

    /** @var Route */
    private $route;

    /**
     * @param Route    $route
     * @param string[] $arguments
     */
    public function __construct(Route $route, array $arguments)
    {
        $this->arguments = $arguments;
        $this->route     = $route;
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }
}
