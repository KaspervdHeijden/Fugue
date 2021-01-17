<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Fugue\Collection\PropertyBag;

use function mb_strtoupper;
use function strcasecmp;
use function rtrim;

final class Request
{
    public const METHOD_GET     = 'GET';
    public const METHOD_POST    = 'POST';
    public const METHOD_HEAD    = 'HEAD';
    public const METHOD_DELETE  = 'DELETE';
    public const METHOD_PUT     = 'PUT';
    public const METHOD_PATCH   = 'PATCH';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_CONNECT = 'CONNECT';
    public const METHOD_TRACE   = 'TRACE';

    private PropertyBag $get;
    private PropertyBag $post;
    private PropertyBag $cookie;
    private PropertyBag $files;
    private PropertyBag $server;
    private PropertyBag $env;

    private ?string $protocol = null;
    private ?string $method = null;
    private ?bool $secure = null;
    private ?Url $url = null;

    public function __construct(
        PropertyBag $get,
        PropertyBag $post,
        PropertyBag $cookie,
        PropertyBag $files,
        PropertyBag $server,
        PropertyBag $env
    ) {
        $this->cookie = $cookie;
        $this->server = $server;
        $this->files  = $files;
        $this->post   = $post;
        $this->env    = $env;
        $this->get    = $get;
    }

    public function getUrl(): Url
    {
        if (! $this->url instanceof Url) {
            $host      = rtrim($this->server->getString('HTTP_HOST'), '/');
            $path      = $this->server->getString('REQUEST_URI');
            $protocol  = ($this->isSecure()) ? 'https' : 'http';

            $this->url = new Url("{$protocol}://{$host}{$path}");
        }

        return $this->url;
    }

    public function isSecure(): bool
    {
        if ($this->secure === null) {
            $https = $this->server->getString('HTTPS', '');

            if ($https === '') {
                $port = $this->server->getInt('SERVER_PORT', 80);
                $this->secure = ($port === 443);
            } else {
                $this->secure = (int)$https !== 0 && strcasecmp($https, 'off') !== 0;
            }
        }

        return $this->secure;
    }

    public function getProtocol(): string
    {
        if ($this->protocol === null) {
            $this->protocol = mb_strtoupper($this->server->get(
                'SERVER_PROTOCOL',
                'HTTP/1.0'
            ));
        }

        return $this->protocol;
    }

    public function getMethod(): string
    {
        if ($this->method === null) {
            $this->method = mb_strtoupper($this->server->getString(
                'REQUEST_METHOD',
                self::METHOD_GET
            ));
        }

        return $this->method;
    }

    public function isHeadRequest(): bool
    {
        return ($this->getMethod() === self::METHOD_HEAD);
    }

    public function isGetRequest(): bool
    {
        return ($this->getMethod() === self::METHOD_GET);
    }

    public function isPostRequest(): bool
    {
        return ($this->getMethod() === self::METHOD_POST);
    }

    public function server(): PropertyBag
    {
        return $this->server;
    }

    public function cookie(): PropertyBag
    {
        return $this->cookie;
    }

    public function get(): PropertyBag
    {
        return $this->get;
    }

    public function post(): PropertyBag
    {
        return $this->post;
    }

    public function files(): PropertyBag
    {
        return $this->files;
    }

    public function env(): PropertyBag
    {
        return $this->env;
    }

    public static function fromArrays(
        array $server,
        array $env,
        array $get    = [],
        array $post   = [],
        array $cookie = [],
        array $files  = []
    ): self {
        return new self(
            new PropertyBag($get),
            new PropertyBag($post),
            new PropertyBag($cookie),
            new PropertyBag($files),
            new PropertyBag($server),
            new PropertyBag($env),
        );
    }
}
