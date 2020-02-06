<?php

declare(strict_types=1);

namespace Fugue\Localization\Formatting\PhoneNumber;

use function mb_substr;

final class DutchPhoneNumberFormatter implements PhoneNumberFormatterInterface
{
    public function format(string $phoneNumber): string
    {
        if ($phoneNumber[0] !== '0') {
            return $phoneNumber;
        }

        switch ($phoneNumber[1]) {
            case '0':
                return $this->format(mb_substr($phoneNumber, 2));
            case '6':
                return '06 ' . mb_substr($phoneNumber, 2);
            case '9':
                switch ($phoneNumber[2]) {
                    case '1':
                        return mb_substr($phoneNumber, 0, 3) . ' ' . mb_substr($phoneNumber, 3);
                    case '0':
                    case '7':
                    default:
                        return mb_substr($phoneNumber, 0, 4) . ' ' . mb_substr($phoneNumber, 4);
                }
            case '8':
                if ($phoneNumber[2] === '0') {
                    return mb_substr($phoneNumber, 0, 4) . ' ' . mb_substr($phoneNumber, 4);
                } else {
                    return mb_substr($phoneNumber, 0, 3) . ' ' . mb_substr($phoneNumber, 3);
                }
            default:
                return mb_substr($phoneNumber, 0, 3) . ' ' . mb_substr($phoneNumber, 3);
        }
    }
}
