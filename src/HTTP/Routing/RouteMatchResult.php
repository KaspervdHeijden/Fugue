<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\Collection\Collection;

final class RouteMatchResult
{
    private Collection $arguments;
    private Route $route;

    public function __construct(
        Route $route,
        Collection $arguments
    ) {
        $this->arguments = $arguments;
        $this->route     = $route;
    }

    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }
}
