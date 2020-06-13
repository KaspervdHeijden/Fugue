<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Mailing\Mailer\EmailSenderInterface;
use Fugue\Mailing\MailPart\PlainTextMessage;
use Fugue\Mailing\Recipient\RecipientList;
use Fugue\Mailing\Recipient\EmailAddress;
use Fugue\Mailing\Recipient\ToRecipient;
use Fugue\Mailing\Email;
use Throwable;

use function basename;

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

    private function getSubject(Throwable $exception): string
    {
        $fileName = basename($exception->getFile());
        return "Exception @ {$fileName}:{$exception->getLine()}";
    }

    public function handle(Throwable $exception): void
    {
        $email = new Email(
            RecipientList::forValues(new ToRecipient(new EmailAddress($this->recipientEmail))),
            new PlainTextMessage($this->formatExceptionMessage($exception)),
            $this->getSubject($exception),
            new EmailAddress($this->senderEmail)
        );

        $this->mailerService->send($email);
    }
}
