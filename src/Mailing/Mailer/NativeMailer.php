<?php

declare(strict_types=1);

namespace Fugue\Mailing\Mailer;

use function mail;

final class NativeMailer extends Mailer
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
