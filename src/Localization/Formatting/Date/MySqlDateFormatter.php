<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

final class MySqlDateFormatter extends DateFormatter
{
    public function format($date): string
    {
        if (! $date) {
            return '';
        }

        $dateTime = $this->getDateTime($date);
        return $dateTime->format('Y-m-d H:i:s');
    }
}
