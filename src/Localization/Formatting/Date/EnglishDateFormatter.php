<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

final class EnglishDateFormatter extends DateFormatter
{
    /** @var string */
    private const DATE_FORMAT = 'l, F jS Y';

    public function format($date): string
    {
        if (! $date) {
            return '';
        }

        $dateTime = $this->getDateTime($date);
        return $dateTime->format(self::DATE_FORMAT);
    }
}
