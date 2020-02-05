<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\PhoneNumber;

final class EnglishPhoneNumberFormatter implements PhoneNumberFormatterInterface
{
    public function format(string $phoneNumber): string
    {
        return $phoneNumber;
    }
}
