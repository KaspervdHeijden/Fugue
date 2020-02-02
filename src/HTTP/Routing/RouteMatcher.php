<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\HTTP\Request;
use Fugue\HTTP\URL;

use function array_filter;
use function in_array;

final class RouteMatcher
{
    /** @var RouteCollectionMap */
    private $routeMap;

    public function __construct(RouteCollectionMap $routeMap)
    {
        $this->routeMap = $routeMap;
    }

    /**
     * Gives a value if this route matches the given request.
     *
     * @param Route  $route          The route to test.
     * @param URL    $url            The URL path to match against.
     *
     * @param string $method         The request method
     * @return RouteMatchResult|null The result of the match.
     */
    private function match(Route $route, URL $url, string $method): ?RouteMatchResult
    {
        if (! in_array($route->getMethod(), [null, $method], true)) {
            return null;
        }

        $matches = [];
        if (! (bool)preg_match($route->getRegex(), $url->getPath(), $matches)) {
            return null;
        }

        return new RouteMatchResult(
            $route,
            array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)
        );
    }

    /**
     * Finds the first route that matches the given request, and runs it.
     *
     * @param Request $request  The request to run.
     * @return RouteMatchResult The response generated from the first route that match the URL.
     */
    public function findForRequest(Request $request): RouteMatchResult
    {
        $method = $request->getMethod();
        $url    = $request->getURL();

        foreach ($this->routeMap as $route) {
            $result = $this->match($route, $url, $method);
            if ($result instanceof RouteMatchResult) {
                return $result;
            }
        }

        throw new RouteNotFoundException(
            "Route not found for {$method} '{$url->getPath()}'."
        );
    }
}
