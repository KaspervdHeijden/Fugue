<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Number;

use Fugue\Localization\Formatting\FormatterInterface;

interface NumberFormatterInterface extends FormatterInterface
{
    public function format($value, int $precision): string;
}
