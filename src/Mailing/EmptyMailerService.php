<?php

declare(strict_types=1);

namespace Fugue\Mailing;

final class EmptyMailerService extends MailerService
{
    protected function sendMail(
        string $to,
        string $subject,
        string $body,
        string $headers
    ): void {
    }
}
