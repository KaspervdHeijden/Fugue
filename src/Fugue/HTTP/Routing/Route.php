<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\HTTP\Request;

use function mb_strtoupper;
use function trim;

final class Route
{
    private ?string $method;
    private string $name;
    private string $url;
    /** @var callable|string */
    private $handler;

    private function __construct(
        string $name,
        string $url,
        string $method,
        $handler
    ) {
        $this->method  = ($method !== '') ? mb_strtoupper(trim($method)) : null;
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
     */
    public static function any(
        string $url,
        $handler,
        string $name
    ): self {
        return new self($name, $url, '', $handler);
    }

    public static function get(
        string $url,
        $handler,
        string $name
    ): self {
        return new self($name, $url, Request::METHOD_GET, $handler);
    }

    public static function post(
        string $url,
        $handler,
        string $name
    ): self {
        return new self($name, $url, Request::METHOD_POST, $handler);
    }

    public static function put(
        string $url,
        $handler,
        string $name
    ): self {
        return new self($name, $url, Request::METHOD_PUT, $handler);
    }

    public static function delete(
        string $url,
        $handler,
        string $name
    ): self {
        return new self($name, $url, Request::METHOD_DELETE, $handler);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    /** @return callable|string */
    public function getHandler()
    {
        return $this->handler;
    }
}
