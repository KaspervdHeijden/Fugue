<?php

declare(strict_types=1);

namespace Fugue\Localization;

interface PhoneNumberDisplayAdapterInterface
{
    /**
     * Display a formatted phone number.
     *
     * @param mixed $phoneNumber The phone number to display.
     * @return string            The formatted phone number.
     */
    public function format(string $phoneNumber): string;
}
