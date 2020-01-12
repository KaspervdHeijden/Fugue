<?php

declare(strict_types=1);

namespace Fugue\Localization\Implementation;

use Fugue\Localization\NumberFormatterInterface;

use function number_format;
use function round;

final class EnglishNumberFormatter implements NumberFormatterInterface
{
    public function format($number, int $precision): string
    {
        return (string)number_format(
            round($number, $precision),
            $precision,
            '.',
            ''
        );
    }
}
