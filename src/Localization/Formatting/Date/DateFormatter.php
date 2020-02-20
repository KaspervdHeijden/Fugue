<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

use DateTimeImmutable;
use DateTimeInterface;
use IntlDateFormatter;

abstract class DateFormatter implements DateFormatterInterface
{
    /** @var IntlDateFormatter */
    private $formatter;

    /**
     * Gets a locale formatter.
     *
     * @param string $languageCode  The language code for the formatter.
     * @param string $format        The format to use.
     *
     * @return IntlDateFormatter    A formatter in the supplied language.
     */
    protected function getFormatter(
        string $languageCode,
        string $format
    ): IntlDateFormatter {
        if (! $this->formatter instanceof IntlDateFormatter) {
            $this->formatter = new IntlDateFormatter(
                $languageCode,
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                null,
                IntlDateFormatter::GREGORIAN,
                $format
            );
        }

        return $this->formatter;
    }

    final protected function getDateTime(string $date): DateTimeInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new DateTimeImmutable($date);
    }
}
