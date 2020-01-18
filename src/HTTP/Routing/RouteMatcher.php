<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\Container\ClassResolver;
use Fugue\Container\Container;
use Fugue\Collection\Map;
use Fugue\HTTP\Response;
use Fugue\HTTP\Request;
use RuntimeException;

use function call_user_func_array;
use function method_exists;
use function class_exists;
use function is_callable;
use function explode;

final class RouteMatcher
{
    public const DEFAULT_CONTROLLER_METHOD = 'handleRequest';

    /** @var Map */
    private $objectMapping;

    /** @var Container */
    private $container;

    /** @var RouteMap */
    private $routeMap;

    public function __construct(Map $objectMapping, Container $container, RouteMap $routeMap)
    {
        $this->objectMapping = $objectMapping;
        $this->container     = $container;
        $this->routeMap      = $routeMap;
    }

    /**
     * Finds the first route that matches the given request, and runs it.
     *
     * @param Request $request        The request to run.
     *
     * @return Response               The response generated from the first route that match the URL.
     * @throws RouteNotFoundException If no matching route was found.
     */
    public function findAndRun(Request $request): Response
    {
        $method = $request->getMethod();
        $url    = $request->getURL();

        /** @var Route $route */
        foreach ($this->routeMap as $route) {
            switch ($route->getMethod()) {
                case '': // fall through
                case $method:
                    $result = $route->match($url);
                    if ($result->matches()) {
                        return $this->run(
                            $route,
                            $request,
                            $result->getArguments()
                        );
                    }

                    break;
            }
        }

        throw new RouteNotFoundException(
            "Route not found for {$method} '{$url->getPath()}'."
        );
    }

    /**
     * Gets the handler.
     *
     * @param Route   $route   The route to run.
     * @param Request $request The originating request.
     *
     * @return callable        The handler for the route.
     */
    private function getHandler(Route $route, Request $request): callable
    {
        $handler = $route->getHandler();
        if (is_callable($handler)) {
            return $handler;
        }

        [$className, $methodName] = explode('@', "\\{$handler}", 2);
        if ($className === '' || ! class_exists($className, true)) {
            throw new RuntimeException("Cannot load class '{$className}'.");
        }

        if (! isset($methodName) || $methodName === '') {
            $methodName = self::DEFAULT_CONTROLLER_METHOD;
        }

        $mapping = $this->objectMapping->merge(new Map([
            Container::class => $this->container,
            RouteMap::class  => $this->routeMap,
            Request::class   => $request,
            Route::class     => $route,
        ]));

        $instance = (new ClassResolver())->resolve($className, $this->container, $mapping);
        if (! method_exists($instance, $methodName)) {
            throw new RuntimeException(
                "Handler function does not exist: '{$className}->{$methodName}()'."
            );
        }

        return [$instance, $methodName];
    }

    /**
     * Runs a route.
     *
     * @param Route   $route   The route to run.
     * @param Request $request The request object to use as input.
     * @param mixed[] $params  The parameters used to call the handler.
     *
     * @return Response        The response.
     */
    private function run(Route $route, Request $request, array $params): Response
    {
        $handler = $this->getHandler($route, $request);
        return call_user_func_array($handler, $params);
    }
}
