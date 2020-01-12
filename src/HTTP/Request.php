<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Fugue\Collection\PropertyBag;

use function mb_strtoupper;
use function strcasecmp;
use function rtrim;

final class Request
{
    /** @var string */
    public const METHOD_GET     = 'GET';

    /** @var string */
    public const METHOD_POST    = 'POST';

    /** @var string */
    public const METHOD_HEAD    = 'HEAD';

    /** @var string */
    public const METHOD_DELETE  = 'DELETE';

    /** @var string */
    public const METHOD_PUT     = 'PUT';

    /** @var string */
    public const METHOD_PATCH   = 'PATCH';

    /** @var string */
    public const METHOD_OPTIONS = 'OPTIONS';

    /** @var string */
    public const METHOD_CONNECT = 'CONNECT';

    /** @var string */
    public const METHOD_TRACE   = 'TRACE';

    /** @var PropertyBag */
    private $server;

    /** @var PropertyBag */
    private $cookie;

    /** @var PropertyBag */
    private $files;

    /** @var PropertyBag */
    private $post;

    /** @var PropertyBag */
    private $get;

    /**
     * Creates a request object.
     *
     * @param PropertyBag $get    The GET data.
     * @param PropertyBag $post   The POST data.
     * @param PropertyBag $cookie The COOKIE data.
     * @param PropertyBag $files  The FILES data.
     * @param PropertyBag $server The SERVER data.
     */
    public function __construct(
        PropertyBag $get,
        PropertyBag $post,
        PropertyBag $cookie,
        PropertyBag $files,
        PropertyBag $server
    ) {
        $this->cookie = $cookie;
        $this->server = $server;
        $this->files  = $files;
        $this->post   = $post;
        $this->get    = $get;
    }

    /**
     * Creates a Request object from the super globals.
     *
     * @return Request
     */
    public static function fromSuperGlobals(): Request
    {
        return new self(
            new PropertyBag($_GET),
            new PropertyBag($_POST),
            new PropertyBag($_COOKIE),
            new PropertyBag($_FILES),
            new PropertyBag($_SERVER)
        );
    }

    /**
     * Gets the URL of the current Request.
     *
     * @return URL The current Request URL.
     */
    public function getURL(): URL
    {
        return new URL(
            $this->getBaseURL()->getURL() .
            $this->server->getString('REQUEST_URI')
        );
    }

    /**
     * Gets the URL of the current Request.
     *
     * @return URL The current Request URL.
     */
    public function getBaseURL(): URL
    {
        $host     = rtrim($this->server->getString('HTTP_HOST'), '/');
        $protocol = ($this->isSecure()) ? 'https' : 'http';

        return new URL("{$protocol}://{$host}");
    }

    /**
     * Gets a value indicating if this request is being done over a secure connection.
     *
     * @return bool TRUE if this is a secure request, FALSE otherwise.
     */
    public function isSecure(): bool
    {
        $https = $this->server->getString('HTTPS', '');
        if ($https === '') {
            return ($this->server->getInt('SERVER_PORT', 80) === 443);
        }

        return ((int)$https !== 0 && strcasecmp($https, 'off') !== 0);
    }

    /**
     * Gets the Request protocol used for this request.
     *
     * @return string The request protocol.
     */
    public function getProtocol(): string
    {
        return mb_strtoupper($this->server->get('SERVER_PROTOCOL', 'HTTP/1.0'));
    }

    /**
     * Gets the Request method used for this request.
     *
     * @return string The request method.
     */
    public function getMethod(): string
    {
        return mb_strtoupper($this->server->getString('REQUEST_METHOD', self::METHOD_GET));
    }

    /**
     * Is this a HEAD request?
     *
     * @return bool
     */
    public function isHeadRequest(): bool
    {
        return ($this->getMethod() === self::METHOD_HEAD);
    }

    /**
     * Is this a GET request?
     *
     * @return bool
     */
    public function isGetRequest(): bool
    {
        return ($this->getMethod() === self::METHOD_GET);
    }

    /**
     * Is this a POST request?
     *
     * @return bool
     */
    public function isPostRequest(): bool
    {
        return ($this->getMethod() === self::METHOD_POST);
    }

    /**
     * Gets the SERVER PropertyBag.
     *
     * @return PropertyBag The $_SERVER PropertyBag.
     */
    public function server(): PropertyBag
    {
        return $this->server;
    }

    /**
     * Gets the COOKIE PropertyBag.
     *
     * @return PropertyBag The $_COOKIE PropertyBag.
     */
    public function cookie(): PropertyBag
    {
        return $this->cookie;
    }

    /**
     * Gets the GET PropertyBag.
     *
     * @return PropertyBag The $_GET PropertyBag.
     */
    public function get(): PropertyBag
    {
        return $this->get;
    }

    /**
     * Gets the POST PropertyBag.
     *
     * @return PropertyBag The $_POST PropertyBag.
     */
    public function post(): PropertyBag
    {
        return $this->post;
    }

    /**
     * Gets the FILES PropertyBag.
     *
     * @return PropertyBag The $_FILES PropertyBag.
     */
    public function files(): PropertyBag
    {
        return $this->files;
    }
}
