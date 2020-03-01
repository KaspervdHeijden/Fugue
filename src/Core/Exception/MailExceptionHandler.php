<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Mailing\EmailSenderInterface;
use Fugue\Mailing\HTMLMessage;
use Fugue\Mailing\Email;
use Throwable;

use function basename;

final class MailExceptionHandler extends ExceptionHandler
{
    /** @var EmailSenderInterface */
    private $mailerService;

    /** @var string */
    private $recipientEmail;

    /** @var string */
    private $senderEmail;

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
            $this->recipientEmail,
            $this->senderEmail,
            $this->getSubject($exception),
            new HTMLMessage($this->formatExceptionMessage($exception)),
            null,
            []
        );

        $this->mailerService->send($email);
    }
}
