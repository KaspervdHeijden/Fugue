<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

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
use function class_exists;
use function is_callable;
use function array_merge;
use function implode;
use function explode;
use function header;

final class HttpRuntime implements RuntimeInterface
{
    /**
     * @var string The default controller method name if not set.
     */
    private const DEFAULT_CONTROLLER_METHOD = 'handleRequest';

    private OutputHandlerInterface $outputHandler;
    private ClassResolver $classResolver;
    private RouteCollectionMap $routeMap;
    private Container $container;

    public function __construct(
        OutputHandlerInterface $outputHandler,
        RouteCollectionMap $routeMap,
        ClassResolver $classResolver,
        Container $container
    ) {
        $this->outputHandler = $outputHandler;
        $this->classResolver = $classResolver;
        $this->container     = $container;
        $this->routeMap      = $routeMap;
    }

    public function handle(Request $request): void
    {
        $matcher     = new RouteMatcher($this->routeMap);
        $matchResult = $matcher->getRouteForRequest($request);
        $response    = $this->run($matchResult, $request);

        $this->sendHeaders($request, $response);
        $this->outputHandler->write($response->getContent()->value());
    }

    private function sendHeaders(
        Request $request,
        Response $response
    ): void {
        if (headers_sent()) {
            return;
        }

        $headers = $this->getHeaders($request, $response);
        $code    = $response->getStatusCode();

        header_remove();
        foreach ($headers as $header) {
            header($header, true, $code);
        }
    }

    private function getHeaders(
        Request $request,
        Response $response
    ): array {
        $headers      = $response->getHeaders();
        $statusHeader = implode(' ', [
            $request->getProtocol(),
            $response->getStatusCode(),
            $response->getStatusCodeText(),
        ]);

        if ($this->shouldSendContentLength($request, $response)) {
            $headers->set(Header::contentLength($response->getContent()->length()));
        }

        return array_merge(
            [$statusHeader],
            array_map(
                static function (Header $header): string {
                    return $header->toHeaderString();
                },
                $headers->toArray()
            )
        );
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

    private function getHandler(
        Route $route,
        Request $request
    ): callable {
        $handler = $route->getHandler();
        if (is_callable($handler)) {
            return $handler;
        }

        [$className, $methodName] = explode('@', "\\{$handler}", 2);
        if (($className ?? '') === '' || ! class_exists($className, true)) {
            throw InvalidRouteHandlerException::nonExistentClass($className);
        }

        $mapping  = [Request::class => $request, Route::class => $route];
        $instance = $this->classResolver->resolve(
            $className,
            $this->container,
            new CollectionMap($mapping)
        );

        if ($methodName === '' && is_callable($instance)) {
            return $instance;
        }

        if (($methodName ?? '') === '') {
            $methodName = self::DEFAULT_CONTROLLER_METHOD;
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
        $arguments = array_merge(
            $matchResult->getArguments(),
            [$request, $matchResult->getRoute()]
        );

        return $handler(...$arguments);
    }
}
