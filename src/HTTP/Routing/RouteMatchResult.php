<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\Collection\CollectionList;

final class RouteMatchResult
{
    private CollectionList $arguments;
    private Route $route;

    public function __construct(
        Route $route,
        CollectionList $arguments
    ) {
        $this->arguments = $arguments;
        $this->route     = $route;
    }

    public function getArguments(): CollectionList
    {
        return $this->arguments;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }
}
