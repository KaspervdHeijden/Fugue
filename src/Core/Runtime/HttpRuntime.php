<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\HTTP\ResponseHeadersHandlerInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\HTTP\Routing\RouteMatchResult;
use Fugue\HTTP\Routing\RouteMatcher;
use Fugue\Collection\CollectionMap;
use Fugue\Container\ClassResolver;
use Fugue\Container\Container;
use Fugue\HTTP\Routing\Route;
use Fugue\HTTP\Response;
use Fugue\HTTP\Request;

use function method_exists;
use function is_callable;
use function explode;

final class HttpRuntime implements RuntimeInterface
{
    private ResponseHeadersHandlerInterface $responseHeadersHandler;
    private OutputHandlerInterface $outputHandler;
    private ClassLoaderInterface $classLoader;
    private ClassResolver $classResolver;
    private RouteCollectionMap $routes;
    private Container $container;

    public function __construct(
        ResponseHeadersHandlerInterface $responseHeadersHandler,
        OutputHandlerInterface $outputHandler,
        ClassLoaderInterface $classLoader,
        ClassResolver $classResolver,
        RouteCollectionMap $routes,
        Container $container
    ) {
        $this->responseHeadersHandler = $responseHeadersHandler;
        $this->outputHandler          = $outputHandler;
        $this->classResolver          = $classResolver;
        $this->classLoader            = $classLoader;
        $this->container              = $container;
        $this->routes                 = $routes;
    }

    public function handle(Request $request): void
    {
        $matcher     = new RouteMatcher($this->routes);
        $matchResult = $matcher->getRouteForRequest($request);
        $response    = $this->run($matchResult, $request);

        $this->responseHeadersHandler->sendHeaders($request, $response);
        $this->outputHandler->write($response->getContent()->value());
    }

    private function getHandler(Route $route, Request $request): callable
    {
        $handler = $route->getHandler();
        if (is_callable($handler)) {
            return $handler;
        }

        [$className, $methodName] = explode('@', "\\{$handler}", 2);
        if (($className ?? '') === '' || ! $this->classLoader->exists($className, true)) {
            throw InvalidRouteHandlerException::nonExistentClass($className);
        }

        $mapping  = new CollectionMap([Request::class => $request, Route::class => $route]);
        $instance = $this->classResolver->resolve($className, $this->container, $mapping);

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

    private function run(
        RouteMatchResult $matchResult,
        Request $request
    ): Response {
        $handler   = $this->getHandler($matchResult->getRoute(), $request);
        $arguments = $matchResult->getArguments()->merge([$request, $matchResult->getRoute()]);

        return $handler(...$arguments);
    }
}
