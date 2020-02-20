<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Number;

use function number_format;
use function round;

final class EnglishNumberFormatter implements NumberFormatterInterface
{
    public function format(float $number, int $precision): string
    {
        return (string)number_format(
            (float)round($number, $precision),
            $precision,
            '.',
            ''
        );
    }
}
