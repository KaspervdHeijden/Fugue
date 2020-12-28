<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Mailing\Mailer\EmailSenderInterface;
use Fugue\Mailing\Email;
use Throwable;

final class MailExceptionHandler extends ExceptionHandler
{
    private EmailSenderInterface $mailerService;
    private string $recipientEmail;
    private string $senderEmail;

    public function __construct(
        string $recipientEmail,
        string $senderEmail,
        EmailSenderInterface $mailerService
    ) {
        $this->recipientEmail = $recipientEmail;
        $this->mailerService  = $mailerService;
        $this->senderEmail    = $senderEmail;
    }

    private function getSubject(Throwable $throwable): string
    {
        return "Exception on {$throwable->getFile()}:{$throwable->getLine()}";
    }

    public function handle(Throwable $throwable): void
    {
        $email = Email::forText(
            $this->recipientEmail,
            $this->getSubject($throwable),
            $this->formatExceptionMessage($throwable),
            $this->senderEmail
        );

        $this->mailerService->send($email);
    }
}
