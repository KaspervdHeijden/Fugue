<?php

declare(strict_types=1);

namespace Fugue\Mailing\Mailer;

use Fugue\Mailing\Email;

interface EmailSenderInterface
{
    public function send(Email $email): void;
}
