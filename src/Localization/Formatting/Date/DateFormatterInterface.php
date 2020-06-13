<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

use Fugue\Localization\Formatting\FormatterInterface;

interface DateFormatterInterface extends FormatterInterface
{
    public function format($value): string;
}
