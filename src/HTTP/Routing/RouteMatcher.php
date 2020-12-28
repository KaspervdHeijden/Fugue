<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\HTTP\Request;

use const ARRAY_FILTER_USE_KEY;
use function preg_replace_callback;
use function mb_strtolower;
use function array_filter;
use function str_replace;
use function preg_match;
use function in_array;
use function rtrim;

final class RouteMatcher
{
    private const URL_TEMPLATE_REGEX = '#\{([a-z_][a-z0-9_]+)(\:[sif])?\}#iu';

    private RouteCollectionMap $routeMap;

    public function __construct(RouteCollectionMap $routeMap)
    {
        $this->routeMap = $routeMap;
    }

    private function getRegularExpressionForRoute(Route $route): string
    {
        $regex = str_replace('/', '/+', rtrim(preg_replace_callback(
            self::URL_TEMPLATE_REGEX,
            static function (array $matches): string {
                switch (isset($matches[2]) && $matches[2] !== '' ? mb_strtolower($matches[2][1]) : 's') {
                    case 'i':
                        $regex = '\d+';
                        break;
                    case 'f':
                        $regex = '\d+(\.\d+)?';
                        break;
                    default:
                        $regex = '[^/]+?';
                        break;
                }

                return "(?<{$matches[1]}>{$regex})";
            },
            $route->getUrl()
        ), '/'));

        return "#^{$regex}\/*$#";
    }

    private function match(
        Route $route,
        Request $request
    ): ?RouteMatchResult {
        if (! in_array($route->getMethod(), [null, $request->getMethod()], true)) {
            return null;
        }

        $path    = $request->getUrl()->getPath();
        $regex   = $this->getRegularExpressionForRoute($route);
        $matches = [];

        if (! (bool)preg_match($regex, $path, $matches)) {
            return null;
        }

        $arguments = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        return new RouteMatchResult($route, $arguments);
    }

    public function getUrl(
        string $routeName,
        array $parameters
    ): string {
        $route = $this->routeMap->get($routeName);
        if (! $route instanceof Route) {
            return '';
        }

        return preg_replace_callback(
            self::URL_TEMPLATE_REGEX,
            static function (array $matches) use ($parameters): string {
                return mb_strtolower($params[$matches[1]] ?? '');
            },
            $route->getUrl()
        );
    }

    public function getRouteForRequest(Request $request): RouteMatchResult
    {
        foreach ($this->routeMap as $route) {
            $result = $this->match($route, $request);
            if ($result instanceof RouteMatchResult) {
                return $result;
            }
        }

        throw RouteNotFoundException::forRequest($request);
    }
}
