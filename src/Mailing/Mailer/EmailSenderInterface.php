<?php

declare(strict_types=1);

namespace Fugue\Mailing\Mailer;

use Fugue\Mailing\Email;

interface EmailSenderInterface
{
    /**
     * Sends an email.
     *
     * @param Email $email The e-mail to send.
     */
    public function send(Email $email): void;
}
