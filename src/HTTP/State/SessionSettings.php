<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

final class SessionSettings
{
    /** @var bool */
    private $useOnlyCookies;

    /** @var int */
    private $cacheExpire;

    /** @var bool */
    private $useCookies;

    /** @var int */
    private $httpOnly;

    /** @var int */
    private $timeout;

    /** @var bool */
    private $secure;

    /** @var string */
    private $name;

    public function __construct(
        string $name,
        bool $useOnlyCookies,
        int $cacheExpire,
        bool $useCookies,
        int $httpOnly,
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

    public function getHttpOnly(): int
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
