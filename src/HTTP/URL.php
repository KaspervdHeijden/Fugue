<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use RuntimeException;

use function parse_url;
use function is_array;

final class URL
{
    /**
     * @var int The default port number.
     */
    public const DEFAULT_PORT = 80;

    /** @var string[]|null */
    private $parts;

    /** @var string */
    private $url;

    /**
     * Constructs a URL.
     *
     * @param string $url The initial URL to use.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Gets a part from the URL.
     *
     * @param string $part The part to get.
     * @return string      The requested URL part.
     */
    private function getPart(string $part): string
    {
        if ($this->parts === null) {
            $parts = parse_url($this->url);
            if (! is_array($parts)) {
                throw new RuntimeException("Could not parse URL '{$this->url}'");
            }

            $this->parts = $parts;
        }

        return (string)($this->parts[$part] ?? '');
    }

    /**
     * Gets the current URL.
     *
     * @return string The current URL.
     */
    public function getURL(): string
    {
        return $this->url;
    }

    /**
     * Gets the fragment of the URL.
     *
     * @return string The scheme.
     */
    public function getFragment(): string
    {
        return $this->getPart('fragment');
    }

    /**
     * Gets the host of the URL.
     *
     * @return string The host.
     */
    public function getHost(): string
    {
        return $this->getPart('host');
    }

    /**
     * Gets the password of the URL.
     *
     * @return string The password.
     */
    public function getPassword(): string
    {
        return $this->getPart('pass');
    }

    /**
     * Gets the user of the URL.
     *
     * @return string The user.
     */
    public function getUser(): string
    {
        return $this->getPart('user');
    }

    /**
     * Gets the path of the URL.
     *
     * @return string The path.
     */
    public function getPath(): string
    {
        return $this->getPart('path');
    }

    /**
     * Gets the port of the URL.
     *
     * @return int The port.
     */
    public function getPort(): int
    {
        $port = $this->getPart('port');
        if ($port === '') {
            return self::DEFAULT_PORT;
        }

        return (int)$port;
    }

    /**
     * Gets the query string of the URL.
     *
     * @return string The query string.
     */
    public function getQuery(): string
    {
        return $this->getPart('query');
    }

    /**
     * Gets the scheme of the URL.
     *
     * @return string The scheme.
     */
    public function getScheme(): string
    {
        return $this->getPart('scheme');
    }

    /**
     * Gets a string representation of this URL.
     *
     * @return string The URL.
     */
    public function __toString(): string
    {
        return $this->getURL();
    }
}
