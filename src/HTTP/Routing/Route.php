<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\HTTP\Request;

use function mb_strtoupper;
use function trim;

final class Route
{
    /** @var callable|string */
    private $handler;

    /** @var string|null */
    private ?string $method;
    private string $name;
    private string $url;

    /**
     * Creates a Route.
     *
     * @param string $name             The name of the Route
     * @param string $url              The path match.
     * @param string|null $method      The method used.
     * @param callable|string $handler The handler to perform.
     */
    private function __construct(
        string $name,
        string $url,
        ?string $method,
        $handler
    ) {
        $this->method  = ((string)$method !== '') ? mb_strtoupper(trim($method)) : null;
        $this->handler = $handler;
        $this->name    = $name;
        $this->url     = $url;
    }

    /**
     * Binds a handler to a route, using an URL template.
     *
     * URL templates are a literals that matches an URL. E.G.:<br>
     * <code>/products/</code> will match <i>/products/</i>.
     *
     * Prefixed slashes are not mandatory. So this is equivalent:<br>
     * <code>products/</code> still matches <i>/products/</i>.
     *
     * In addition, variables are supported. Variables take the form {<i>name</i>},
     *  and are passed as an argument to the controller handler function. E.g.:<br>
     * <code>/product/{id}</code> matches <i>/product/what-ever</i>, and "what-ever" is passed to the controller.
     *
     * @param string          $url     The url template that matches the path.
     * @param callable|string $handler The Handler to run.
     * @param string          $name    The name of the Route.
     * @param string          $method  The method used for the match.
     *
     * @return Route                   The added Route.
     */
    public static function any(
        string $url,
        $handler,
        string $name,
        string $method
    ): Route {
        return new static($name, $url, $method, $handler);
    }

    /**
     * Binds a GET handler to a route.
     *
     * @param string          $url     The url template that matches the path.
     * @param callable|string $handler The Handler to run.
     * @param string          $name    The name of the Route.
     *
     * @return Route                   The added Route.
     */
    public static function get(
        string $url,
        $handler,
        string $name
    ): Route {
        return self::any($url, $handler, $name, Request::METHOD_GET);
    }

    /**
     * Binds a POST handler to a route.
     *
     * @param string          $url     The url template that matches the path.
     * @param callable|string $handler The Handler to run.
     * @param string          $name    The name of the Route.
     *
     * @return Route                   The added Route.
     */
    public static function post(
        string $url,
        $handler,
        string $name
    ): Route {
        return self::any($url, $handler, $name, Request::METHOD_POST);
    }

    /**
     * Binds a PUT handler to a route.
     *
     * @param string          $url     The url template that matches the path.
     * @param callable|string $handler The Handler to run.
     * @param string          $name    The name of the Route.
     *
     * @return Route                   The added Route.
     */
    public static function put(
        string $url,
        $handler,
        string $name
    ): Route {
        return self::any($url, $handler, $name, Request::METHOD_PUT);
    }

    /**
     * Binds a DELETE handler to a route.
     *
     * @param string          $url     The url template that matches the path.
     * @param callable|string $handler The handler to run.
     * @param string          $name    The name of the Route.
     *
     * @return Route                   The added Route.
     */
    public static function delete(
        string $url,
        $handler,
        string $name
    ): Route {
        return self::any($url, $handler, $name, Request::METHOD_DELETE);
    }

    /**
     * Gets the name of the Route.
     *
     * @return string The Route's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the match of the Route.
     *
     * @return string The Route's path match.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Gets the method of the Route.
     *
     * @return string The Route's method requirement.
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Gets the handler.
     *
     * @return callable|string
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
