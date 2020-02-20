<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Number;

interface NumberFormatterInterface
{
    /**
     * Formats a number.
     *
     * @param float $number    A numeric value to format.
     * @param int   $precision The precision to use.
     *
     * @return string          The formatted number.
     */
    public function format(float $number, int $precision): string;
}
