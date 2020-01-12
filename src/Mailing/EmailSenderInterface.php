<?php

declare(strict_types=1);

namespace Fugue\Mailing;

/**
 * Defines an object to be capable of sending email.
 */
interface EmailSenderInterface
{
    /**
     * Sends an email.
     *
     * @param Email $email The email to send.
     */
    public function send(Email $email): void;
}
