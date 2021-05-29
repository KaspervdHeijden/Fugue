<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\Collection\Collection;
use Fugue\HTTP\Request;

use function preg_replace_callback;
use function mb_strtolower;
use function str_replace;
use function preg_match;
use function rtrim;

final class RouteMatcher
{
    private const URL_TEMPLATE_REGEX = '#\{([a-z_][a-z0-9_]+)(\:[sif])?\}#iu';

    private RouteCollectionMap $routeMap;

    public function __construct(RouteCollectionMap $routeMap)
    {
        $this->routeMap = $routeMap;
    }

    private function getRegularExpressionAndTypesForRoute(Route $route): object
    {
        $castMap = [];
        $regex   = str_replace('/', '/+', rtrim(preg_replace_callback(
            self::URL_TEMPLATE_REGEX,
            static function (array $matches) use (&$castMap): string {
                switch (isset($matches[2]) && $matches[2] !== '' ? mb_strtolower($matches[2][1]) : 's') {
                    case 'i':
                        $castMap[$matches[1]] = '\intval';
                        $regex                = '\d+';
                        break;
                    case 'f':
                        $castMap[$matches[1]] = '\floatval';
                        $regex                = '\d+(\.\d+)?';
                        break;
                    default:
                        $castMap[$matches[1]] = '\strval';
                        $regex                = '[^/]+?';
                        break;
                }

                return "(?<{$matches[1]}>{$regex})";
            },
            $route->getUrl()
        ), '/'));

        return (object)[
            'regex'   => "#^{$regex}\/*$#",
            'castMap' => $castMap,
        ];
    }

    private function match(Route $route, Request $request): ?RouteMatchResult
    {
        if ($route->getMethod() !== null && $route->getMethod() !== $request->getMethod()) {
            return null;
        }

        $path    = $request->getUrl()->getPath();
        $regex   = $this->getRegularExpressionAndTypesForRoute($route);
        $matches = [];

        if (! (bool)preg_match($regex->regex, $path, $matches)) {
            return null;
        }

        $arguments = [];
        foreach ($matches as $key => $value) {
            if (! isset($regex->castMap[$key])) {
                continue;
            }

            $caster          = $regex->castMap[$key];
            $arguments[$key] = $caster($value);
        }

        return new RouteMatchResult($route, Collection::forMixed($arguments));
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
            static fn (array $matches): string => mb_strtolower($parameters[$matches[1]] ?? ''),
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
