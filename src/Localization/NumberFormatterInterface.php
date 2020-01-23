<?php

declare(strict_types=1);

namespace Fugue\Localization;

interface NumberFormatterInterface
{
    /**
     * Formats a number.
     *
     * @param mixed $number    A numeric value to format.
     * @param int   $precision The precision to use.
     *
     * @return string          The formatted number.
     */
    public function format($number, int $precision): string;
}