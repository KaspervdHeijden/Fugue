<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

final class DatabaseConnectionSettings
{
    private string $password;
    private string $timezone;
    private array $options;
    private string $charset;
    private string $user;
    private string $host;

    public function __construct(
        string $host,
        string $user,
        string $password,
        string $charset,
        string $timezone,
        array $options
    ) {
        $this->password = $password;
        $this->timezone = $timezone;
        $this->options  = $options;
        $this->charset  = $charset;
        $this->user     = $user;
        $this->host     = $host;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }
}
