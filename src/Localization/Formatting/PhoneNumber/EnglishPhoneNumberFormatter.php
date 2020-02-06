<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\PhoneNumber;

use function trim;

final class EnglishPhoneNumberFormatter implements PhoneNumberFormatterInterface
{
    public function format(string $phoneNumber): string
    {
        return trim($phoneNumber);
    }
}
