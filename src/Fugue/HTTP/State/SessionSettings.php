<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

final class SessionSettings
{
    private bool $useOnlyCookies;
    private int $cacheExpire;
    private bool $useCookies;
    private bool $httpOnly;
    private string $name;
    private int $timeout;
    private bool $secure;

    public function __construct(
        string $name,
        bool $useOnlyCookies,
        int $cacheExpire,
        bool $useCookies,
        bool $httpOnly,
        int $timeout,
        bool $secure
    ) {
        $this->useOnlyCookies = $useOnlyCookies;
        $this->cacheExpire    = $cacheExpire;
        $this->useCookies     = $useCookies;
        $this->httpOnly       = $httpOnly;
        $this->timeout        = $timeout;
        $this->secure         = $secure;
        $this->name           = $name;
    }

    public function shouldUseOnlyCookies(): bool
    {
        return $this->useOnlyCookies;
    }

    public function getCacheExpire(): int
    {
        return $this->cacheExpire;
    }

    public function shouldUseCookies(): bool
    {
        return $this->useCookies;
    }

    public function getHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
