<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

final class EnglishDateFormatter extends DateFormatter
{
    /** @var string */
    private const DATE_FORMAT = 'l, F jS Y';

    public function format(string $date): string
    {
        if ($date === '') {
            return '';
        }

        return $this->getDateTime($date)
                    ->format(self::DATE_FORMAT);
    }
}
