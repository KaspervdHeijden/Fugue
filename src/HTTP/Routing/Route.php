<?php

declare(strict_types=1);

namespace Fugue\HTTP\Routing;

use Fugue\HTTP\Request;

use function preg_replace_callback;
use function mb_strtolower;
use function mb_strtoupper;
use function trim;

final class Route
{
    /**
     * @var string The regular expression used to parse the URL templates.
     */
    private const URL_TEMPLATE_REGEX       = '#\{([a-z_][a-z0-9_]+)(\:[sif])?\}#iu';

    /** @var string */
    private $urlTemplate;

    /** @var callable|string */
    private $handler;

    /** @var string|null */
    private $method;

    /** @var string */
    private $name;

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
     * @param string          $urlTemplate The url template that matches the path.
     * @param callable|string $handler     The Handler to run.
     * @param string          $name        The name of the Route.
     * @param string          $method      The method used for the match.
     *
     * @return Route                       The added Route.
     */
    public static function any(string $urlTemplate, $handler, string $name, string $method): Route
    {
        return new static($name, $urlTemplate, $method, $handler);
    }

    /**
     * Binds a GET handler to a route.
     *
     * @param string          $urlTemplate The url template that matches the path.
     * @param callable|string $handler     The Handler to run.
     * @param string          $name        The name of the Route.
     *
     * @return Route                       The added Route.
     */
    public static function get(string $urlTemplate, $handler, string $name = ''): Route
    {
        return self::any($urlTemplate, $handler, $name, Request::METHOD_GET);
    }

    /**
     * Binds a POST handler to a route.
     *
     * @param string          $urlTemplate The url template that matches the path.
     * @param callable|string $handler     The Handler to run.
     * @param string          $name        The name of the Route.
     *
     * @return Route                       The added Route.
     */
    public static function post(string $urlTemplate, $handler, string $name = ''): Route
    {
        return self::any($urlTemplate, $handler, $name, Request::METHOD_POST);
    }

    /**
     * Binds a PUT handler to a route.
     *
     * @param string          $urlTemplate The url template that matches the path.
     * @param callable|string $handler     The Handler to run.
     * @param string          $name        The name of the Route.
     *
     * @return Route                       The added Route.
     */
    public static function put(string $urlTemplate, $handler, string $name = ''): Route
    {
        return self::any($urlTemplate, $handler, $name, Request::METHOD_PUT);
    }

    /**
     * Binds a DELETE handler to a route.
     *
     * @param string          $urlTemplate The url template that matches the path.
     * @param callable|string $handler     The handler to run.
     * @param string          $name        The name of the Route.
     *
     * @return Route                       The added Route.
     */
    public static function delete(string $urlTemplate, $handler, string $name = ''): Route
    {
        return self::any($urlTemplate, $handler, $name, Request::METHOD_DELETE);
    }

    /**
     * Creates a Route.
     *
     * @param string $name             The name of the Route
     * @param string $urlTemplate      The path match.
     * @param string|null $method      The method used.
     * @param callable|string $handler The handler to perform.
     */
    private function __construct(
        string $name,
        string $urlTemplate,
        ?string $method,
        $handler
    ) {
        $this->method      = ((string)$method !== '' ) ? mb_strtoupper(trim($method)) : null;
        $this->urlTemplate = $urlTemplate;
        $this->handler     = $handler;
        $this->name        = $name;
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
    public function getURLTemplate(): string
    {
        return $this->urlTemplate;
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

    /**
     * Gets the regular expression used for matching a URL.
     *
     * @return string The regular expression.
     */
    public function getRegex(): string
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
            $this->getURLTemplate()
        ), '/'));

        return "#^{$regex}\/*$#";
    }

    /**
     * Gets the URL.
     *
     * @param array $params List of variables to replace.
     * @return string       The URL that matches the path.
     */
    public function getURL(array $params = []): string
    {
        return preg_replace_callback(
            self::URL_TEMPLATE_REGEX,
            static function (array $matches) use ($params): string {
                return mb_strtolower($params[$matches[1]] ?? '');
            },
            $this->getURLTemplate()
        );
    }
}
