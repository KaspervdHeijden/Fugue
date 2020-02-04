<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Mailing\MailerService;
use Fugue\Mailing\HTMLMessage;
use InvalidArgumentException;
use Fugue\Mailing\Email;

use function filter_var;

final class MailErrorHandler extends ErrorHandler
{
    /** @var MailerService */
    private $mailerService;

    /** @var string */
    private $recipientEmail;

    /** @var string */
    private $senderEmail;

    public function __construct(
        string $recipientEmail,
        string $senderEmail,
        MailerService $mailerService
    ) {
        $this->recipientEmail = $recipientEmail;
        $this->mailerService  = $mailerService;
        $this->senderEmail    = $senderEmail;
    }

    private function getSubject(UnhandledErrorException $exception): string
    {
        $fileName = basename($exception->getFile());
        return "Exception @ {$fileName}:{$exception->getLine()}";
    }

    protected function handle(UnhandledErrorException $exception): bool
    {
        $email = new Email(
            $this->senderEmail,
            $this->getSubject($exception),
            new HTMLMessage($this->formatExceptionMessage($exception))
        );

        $email->addRecipient(
            $this->recipientEmail,
            Email::RECIPIENT_TYPE_TO
        );

        $this->mailerService->send($email);
        return false;
    }
}
