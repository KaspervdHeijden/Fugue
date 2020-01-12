<?php

declare(strict_types=1);

namespace Fugue\Localization\Implementation;

final class MySqlDateFormatter extends DateFormatter
{
    public function format($date): string
    {
        if (! $date) {
            return '';
        }

        $stamp = $this->getDateTime($date);
        return $stamp->format('Y-m-d H:i:s');
    }
}
