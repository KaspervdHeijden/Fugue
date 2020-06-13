<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Number;

use function number_format;

final class DefaultNumberFormatter implements NumberFormatterInterface
{
    private string $thousandsSeparatorChar;
    private string $decimalPointChar;

    public function __construct(
        string $thousandsSeparatorChar = '.',
        string $decimalPointChar       = ','
    ) {
        $this->thousandsSeparatorChar = $thousandsSeparatorChar;
        $this->decimalPointChar       = $decimalPointChar;
    }

    public function format($value, int $precision): string
    {
        return number_format(
            (float)$value,
            $precision,
            $this->decimalPointChar,
            $this->thousandsSeparatorChar
        );
    }
}
