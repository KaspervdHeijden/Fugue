<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\HTTP\ResponseHeadersHandlerInterface;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\HTTP\Routing\RouteMatchResult;
use Fugue\HTTP\Routing\RouteMatcher;
use Fugue\Container\Container;
use Fugue\HTTP\Routing\Route;
use Fugue\HTTP\Response;
use Fugue\HTTP\Request;
use Fugue\Core\Kernel;

use function method_exists;
use function is_callable;
use function explode;

final class HttpRuntime implements RuntimeInterface
{
    private ResponseHeadersHandlerInterface $responseHeadersHandler;
    private RouteCollectionMap $routes;
    private Container $container;
    private Kernel $kernel;

    public function __construct(
        ResponseHeadersHandlerInterface $responseHeadersHandler,
        RouteCollectionMap $routes,
        Container $container,
        Kernel $kernel
    ) {
        $this->responseHeadersHandler = $responseHeadersHandler;
        $this->container              = $container;
        $this->routes                 = $routes;
        $this->kernel                 = $kernel;
    }

    public function handle(Request $request): void
    {
        $matcher     = new RouteMatcher($this->routes);
        $matchResult = $matcher->getRouteForRequest($request);
        $response    = $this->run($matchResult, $request);

        $this->responseHeadersHandler->sendHeaders($request, $response);
        $this->kernel->getOutputHandler()->write($response->getContent()->value());
    }

    private function getHandler(Route $route): callable
    {
        $handler = $route->getHandler();
        if (is_callable($handler)) {
            return $handler;
        }

        [$className, $methodName] = explode('@', "\\{$handler}", 2);
        if (
            ($className ?? '') === '' ||
            ! $this->kernel->getClassLoader()->exists($className, true)
        ) {
            throw InvalidRouteHandlerException::nonExistentClass($className);
        }

        $instance = $this->kernel->resolveClass($className, $this->container);
        if (($methodName ?? '') === '') {
            if (is_callable($instance)) {
                return $instance;
            }

            throw InvalidRouteHandlerException::emptyFunctionName($className);
        }

        if (! method_exists($instance, $methodName)) {
            throw InvalidRouteHandlerException::nonExistentClassFunction(
                $className,
                $methodName
            );
        }

        return [$instance, $methodName];
    }

    private function run(RouteMatchResult $matchResult, Request $request): Response
    {
        $handler   = $this->getHandler($matchResult->getRoute());
        $arguments = $matchResult->getArguments()->merge([
            $request,
            $matchResult->getRoute()
        ]);

        return $handler(...$arguments->values());
    }
}
