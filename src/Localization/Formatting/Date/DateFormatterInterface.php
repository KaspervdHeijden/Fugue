<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

interface DateFormatterInterface
{
    /**
     * Formats a date.
     *
     * @param string $date A date value to format.
     * @return string     The formatted date.
     */
    public function format(string $date): string;
}
