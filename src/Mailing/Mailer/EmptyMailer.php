<?php

declare(strict_types=1);

namespace Fugue\Mailing\Mailer;

final class EmptyMailer extends Mailer
{
    protected function sendMail(
        string $to,
        string $subject,
        string $body,
        string $headers
    ): void {
    }
}
