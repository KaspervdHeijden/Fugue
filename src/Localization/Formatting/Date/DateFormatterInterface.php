<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

interface DateFormatterInterface
{
    /**
     * Formats a date.
     *
     * @param mixed $date A date value to format.
     *                    It can either be a string in MySQL date format,
     *                    or an integer representing a UNIX timestamp.
     * @return string     The formatted date.
     */
    public function format($date): string;
}
