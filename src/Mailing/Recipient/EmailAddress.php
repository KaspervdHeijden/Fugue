<?php

declare(strict_types=1);

namespace Fugue\Mailing\Recipient;

use InvalidArgumentException;

use const FILTER_VALIDATE_EMAIL;
use function mb_strtolower;
use function filter_var;
use function is_string;

final class EmailAddress
{
    private string $emailAddress;

    public function __construct(string $emailAddress)
    {
        if ($emailAddress === '') {
            throw new InvalidArgumentException(
                'The supplied email address must not be empty.'
            );
        }

        $email = filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
        if (! is_string($email)) {
            throw new InvalidArgumentException(
                'The supplied email address is invalid.'
            );
        }

        $this->emailAddress = (string)mb_strtolower((string)$email);
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function __toString(): string
    {
        return $this->emailAddress;
    }
}
