<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use UnexpectedValueException;

use function parse_url;
use function is_array;

final class Url
{
    public const DEFAULT_PORT = 80;

    private ?array $parts = null;
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    private function getPart(string $part): string
    {
        if ($this->parts === null) {
            $parts = parse_url($this->url);
            if (! is_array($parts)) {
                throw new UnexpectedValueException(
                    "Could not parse URL '{$this->url}'"
                );
            }

            $this->parts = $parts;
        }

        return (string)($this->parts[$part] ?? '');
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getFragment(): string
    {
        return $this->getPart('fragment');
    }

    public function getHost(): string
    {
        return $this->getPart('host');
    }

    public function getPassword(): string
    {
        return $this->getPart('pass');
    }

    public function getUser(): string
    {
        return $this->getPart('user');
    }

    public function getPath(): string
    {
        return $this->getPart('path');
    }

    public function getPort(): int
    {
        $port = $this->getPart('port');
        if ($port === '') {
            return self::DEFAULT_PORT;
        }

        return (int)$port;
    }

    public function getQuery(): string
    {
        return $this->getPart('query');
    }

    public function getScheme(): string
    {
        return $this->getPart('scheme');
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
