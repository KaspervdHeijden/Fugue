<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

final class DatabaseConnectionSettings
{
    private string $password;

    private string $timezone;

    private ?array $options;

    private string $charset;

    private string $user;

    private string $dsn;

    public function __construct(
        string $dsn,
        string $user,
        string $password,
        string $charset,
        string $timezone,
        ?array $options
    ) {
        $this->password = $password;
        $this->timezone = $timezone;
        $this->options  = $options;
        $this->charset  = $charset;
        $this->user     = $user;
        $this->dsn      = $dsn;
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getOptions(): ?array
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
