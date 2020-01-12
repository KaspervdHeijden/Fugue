<?php

declare(strict_types=1);

namespace Fugue\Localization\Language;

final class Language
{
    /** @var string */
    private $isoCode = '';

    /** @var string */
    private $code    = '';

    /** @var string */
    private $name    = '';

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): void
    {
        $this->isoCode = $isoCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
