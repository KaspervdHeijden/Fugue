<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\Core\ClassLoader\ClassLoaderInterface;
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
use Fugue\HTTP\Header;

use function method_exists;
use function header_remove;
use function headers_sent;
use function is_callable;
use function explode;
use function header;

final class HttpRuntime implements RuntimeInterface
{
    private OutputHandlerInterface $outputHandler;
    private ClassLoaderInterface $classLoader;
    private ClassResolver $classResolver;
    private RouteCollectionMap $routes;
    private Container $container;

    public function __construct(
        OutputHandlerInterface $outputHandler,
        ClassLoaderInterface $classLoader,
        RouteCollectionMap $routes,
        ClassResolver $classResolver,
        Container $container
    ) {
        $this->outputHandler = $outputHandler;
        $this->classResolver = $classResolver;
        $this->classLoader   = $classLoader;
        $this->container     = $container;
        $this->routes        = $routes;
    }

    public function handle(Request $request): void
    {
        $matcher     = new RouteMatcher($this->routes);
        $matchResult = $matcher->getRouteForRequest($request);
        $response    = $this->run($matchResult, $request);

        $this->sendHeaders($request, $response);
        $this->outputHandler->write($response->getContent()->value());
    }

    private function sendHeaders(Request $request, Response $response): void
    {
        if (headers_sent()) {
            return;
        }

        $code    = $response->getStatusCode();
        $headers = $response->getHeaders();

        if ($this->shouldSendContentLength($request, $response)) {
            $headers[] = Header::contentLength($response->getContent()->size());
        }

        header_remove();
        header("{$request->getProtocol()} {$code} {$response->getStatusText()}", true, $code);

        /** @var Header $header */
        foreach ($headers as $header) {
            header($header->toHeaderString(), true, $code);
        }
    }

    private function shouldSendContentLength(
        Request $request,
        Response $response
    ): bool {
        switch ($response->getStatusCode()) {
            case Response::HTTP_NOT_MODIFIED:
            case Response::HTTP_NO_CONTENT:
                return false;
        }

        switch (true) {
            case $response->isInformational():
            case $request->isHeadRequest():
            case $response->isRedirect():
                return false;
        }

        return true;
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
