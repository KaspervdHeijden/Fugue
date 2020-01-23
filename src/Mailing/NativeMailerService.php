<?php

declare(strict_types=1);

namespace Fugue\Mailing;

final class NativeMailerService extends MailerService
{
    protected function sendMail(
        string $to,
        string $subject,
        string $body,
        string $headers
    ): void {
        mail($to, $subject, $body, $headers);
    }
}