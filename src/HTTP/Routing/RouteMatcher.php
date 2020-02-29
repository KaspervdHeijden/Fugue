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
    /**
     * @var string The regular expression used to parse the URL templates.
     */
    public const URL_TEMPLATE_REGEX = '#\{([a-z_][a-z0-9_]+)(\:[sif])?\}#iu';

    /** @var RouteCollectionMap */
    private $routeMap;

    public function __construct(RouteCollectionMap $routeMap)
    {
        $this->routeMap = $routeMap;
    }

    /**
     * Gets the regular expression used for matching a URL.
     *
     * @param Route $route The route to get the regular expression for.
     * @return string      The regular expression.
     */
    private function getRegex(Route $route): string
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

    /**
     * Gives a value if this route matches the given request.
     *
     * @param Route   $route         The route to test.
     * @param Request $request       The url path to match against.
     *
     * @return RouteMatchResult|null The result of the match.
     */
    private function match(
        Route $route,
        Request $request
    ): ?RouteMatchResult {
        if (! in_array($route->getMethod(), [null, $request->getMethod()], true)) {
            return null;
        }

        $path    = $request->getUrl()->getPath();
        $regex   = $this->getRegex($route);
        $matches = [];

        if (! (bool)preg_match($regex, $path, $matches)) {
            return null;
        }

        $arguments = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        return new RouteMatchResult($route, $arguments);
    }

    /**
     * Gets the URL.
     *
     * @param string $routeName The name of the route.
     * @param array  $params    List of variables to replace.
     *
     * @return string           The URL that matches the path.
     */
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

    /**
     * Gets the first route that matches the given request.
     *
     * @param Request $request  The request to run.
     * @return RouteMatchResult The response generated from the first route that match the Url.
     */
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
