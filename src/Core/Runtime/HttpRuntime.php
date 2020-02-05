<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

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
use Fugue\Core\Kernel;

use Fugue\Core\Output\OutputHandlerInterface;
use function method_exists;
use function header_remove;
use function headers_sent;
use function class_exists;
use function is_callable;
use function array_merge;
use function implode;
use function explode;
use function header;
use function strlen;

final class HttpRuntime implements RuntimeInterface
{
    /**
     * @var string The default controller method name if not set.
     */
    public const DEFAULT_CONTROLLER_METHOD = 'handleRequest';

    /** @var OutputHandlerInterface */
    private $outputHandler;

    /** @var Container */
    private $container;

    /** @var RouteCollectionMap */
    private $routeMap;

    public function __construct(
        OutputHandlerInterface $outputHandler,
        RouteCollectionMap $routeMap,
        Container $container
    ) {
        $this->outputHandler = $outputHandler;
        $this->container     = $container;
        $this->routeMap      = $routeMap;
    }

    public function handle(Request $request): void
    {
        $matcher     = new RouteMatcher($this->routeMap);
        $matchResult = $matcher->findForRequest($request);
        $response    = $this->run($matchResult, $request);

        $this->sendHeaders($request, $response);
        $this->outputHandler->write($response->getContent());
    }

    /**
     * @param Request  $request  The Request.
     * @param Response $response The Response.
     */
    private function sendHeaders(
        Request $request,
        Response $response
    ): void {
        if (headers_sent()) {
            return;
        }

        header_remove();

        $code = $response->getStatusCode();
        foreach ($this->getHeaders($request, $response) as $header) {
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
            $headers->setFromString(
                Header::NAME_CONTENT_LENGTH,
                (string)strlen($response->getContent())
            );
        }

        return array_merge(
            [$statusHeader],
            array_map(
                static function (Header $header): string {
                    return $header->toHeaderString();
                },
                $headers->all()
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

        if ($request->isHeadRequest() || $response->isInformational() || $response->isRedirect()) {
            return false;
        }

        return true;
    }

    /**
     * Gets the handler.
     *
     * @param Route   $route   The route to run.
     * @param Request $request The originating request.
     *
     * @return callable        The handler for the route.
     */
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

        if (($methodName ?? '') === '') {
            $methodName = self::DEFAULT_CONTROLLER_METHOD;
        }

        $mapping = new CollectionMap([
            Request::class => $request,
            Route::class   => $route,
        ]);

        $instance = (new ClassResolver())->resolve($className, $this->container, $mapping);
        if (! method_exists($instance, $methodName)) {
            throw InvalidRouteHandlerException::nonExistentClassFunction(
                $className,
                $methodName
            );
        }

        return [$instance, $methodName];
    }

    /**
     * Runs a route.
     *
     * @param RouteMatchResult $matchResult The route to run.
     * @param Request          $request     The request object to use as input.
     *
     * @return Response                     The response.
     */
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
