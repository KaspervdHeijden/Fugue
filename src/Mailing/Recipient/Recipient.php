<?php

declare(strict_types=1);

namespace Fugue\Mailing\Recipient;

abstract class Recipient
{
    private EmailAddress $emailAddress;

    public function __construct(EmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }
}
