<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\Date;

final class MySqlDateFormatter extends DateFormatter
{
    public const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

    public function format(string $date): string
    {
        if ($date === '') {
            return '';
        }

        return $this->getDateTime($date)
                    ->format(self::MYSQL_DATE_FORMAT);
    }
}
