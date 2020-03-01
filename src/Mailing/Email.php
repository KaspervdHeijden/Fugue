<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use InvalidArgumentException;
use RuntimeException;

use const  FILTER_VALIDATE_EMAIL;
use function mb_strtolower;
use function filter_var;
use function trim;

final class Email
{
    /** @var int */
    public const RECIPIENT_TYPE_TO  = 0;

    /** @var int */
    public const RECIPIENT_TYPE_BCC = 1;

    /** @var int */
    public const RECIPIENT_TYPE_CC  = 2;

    /** @var EmailAttachment[] */
    private $attachments = [];

    /** @var string[] */
    private $recipients = [];

    /** @var HTMLMessage|null */
    private $htmlMessage;

    /** @var TextMessage|null */
    private $textMessage;

    /** @var string */
    private $subject;

    /** @var string */
    private $replyTo;

    /** @var string */
    private $from;

    public function __construct(
        string $to,
        string $from,
        string $subject,
        ?HTMLMessage $htmlMessage,
        ?TextMessage $textMessage,
        array $attachments = []
    ) {
        if (
            ! $htmlMessage instanceof HTMLMessage &&
            ! $textMessage instanceof TextMessage
        ) {
            throw new RuntimeException(
                'At least either a HTMLMessage or a TextMessage should be given.'
            );
        }

        $this->htmlMessage = $htmlMessage;
        $this->textMessage = $textMessage;

        $this->subject = trim($subject);
        if ($this->subject === '') {
            throw new InvalidArgumentException(
                'Subject must not be empty.'
            );
        }

        foreach ($attachments as $attachment) {
            $this->addAttachment($attachment);
        }

        $this->addRecipient($to, self::RECIPIENT_TYPE_TO);

        $fromEmail     = $this->validateEmailAddress($from);
        $this->replyTo = $fromEmail;
        $this->from    = $fromEmail;
    }

    /**
     * Checks if an email address is valid.
     *
     * @param string $emailAddress      The email address to check.
     *
     * @throws InvalidArgumentException If the email address is empty or invalid.
     * @return string                   The valid email address.
     */
    private function validateEmailAddress(string $emailAddress): string
    {
        if ($emailAddress === '') {
            throw new InvalidArgumentException(
                'The supplied email address must not be empty.'
            );
        }

        $email = filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            throw new InvalidArgumentException(
                'The supplied email address is invalid.'
            );
        }

        return mb_strtolower((string)$email);
    }

    public function addRecipient(
        string $emailAddress,
        int $type = self::RECIPIENT_TYPE_TO
    ): void {
        $this->recipients[$this->validateEmailAddress($emailAddress)] = $type;
    }

    public function getRecipients(): array
    {
        $recipients = [];
        foreach ($this->recipients as $recipient => $type) {
            $recipients[] = [
                'email' => $recipient,
                'type'  => $type,
            ];
        }

        return $recipients;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getHTMLMessage(): ?HTMLMessage
    {
        return $this->htmlMessage;
    }

    public function getTextMessage(): ?TextMessage
    {
        return $this->textMessage;
    }

    public function addAttachment(EmailAttachment $attachment): void
    {
        $this->attachments[] = $attachment;
    }

    /**
     * Gets the attachments for this email.
     *
     * @return EmailAttachment[] All attachments of this email.
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
