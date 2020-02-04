<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use InvalidArgumentException;

use function mb_strtolower;
use function array_search;
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
    private $attachments;

    /** @var HTMLMessage */
    private $htmlMessage;

    /** @var TextMessage */
    private $textMessage;

    /** @var array */
    private $recipients;

    /** @var string */
    private $subject;

    /** @var string */
    private $replyTo;

    /** @var string */
    private $from;

    /**
     * Instantiates an Email.
     *
     * @param string      $from    The sender email.
     * @param string      $subject The subject.
     * @param HTMLMessage $message The message to send.
     */
    public function __construct(string $from, string $subject, HTMLMessage $message)
    {
        $this->attachments  = [];
        $this->recipients   = [];

        $this->setHTMLMessage($message);
        $this->setSubject($subject);
        $this->setReplyTo($from);
        $this->setFrom($from);
    }

    /**
     * Checks if an email address is valid.
     *
     * @param string $emailAddress      The email address to check.
     *
     * @throws InvalidArgumentException If the email address is empty or invalid.
     * @return string                   The valid email address.
     */
    private function getAndCheckEmailAddress(string $emailAddress): string
    {
        if ($emailAddress === '') {
            throw new InvalidArgumentException(
                'The supplied email address cannot be empty.'
            );
        }

        $email = filter_var((string)$emailAddress, FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            throw new InvalidArgumentException(
                'The supplied email address seems to be invalid.'
            );
        }

        return mb_strtolower((string)$email);
    }

    /**
     * Adds a recipients to the recipient list.
     *
     * @param string $emailAddress      The email address to add.
     * @param int $type                 The recipient type. See the Email::RECIPIENT_TYPE_* constants.
     *
     * @throws InvalidArgumentException If the email address is empty or invalid.
     */
    public function addRecipient(string $emailAddress, int $type = self::RECIPIENT_TYPE_TO): void
    {
        $this->recipients[$this->getAndCheckEmailAddress($emailAddress)] = $type;
    }

    /**
     * Removes a recipient from the recipient list.
     *
     * @param string $emailAddress      The email address to add.
     * @param int    $type              The recipient type. See the Email::RECIPIENT_TYPE_* constants.
     *
     * @throws InvalidArgumentException If the email address is empty or invalid.
     */
    public function removeRecipient(
        string $emailAddress,
        int $type = self::RECIPIENT_TYPE_TO
    ): void {
        $email = $this->getAndCheckEmailAddress($emailAddress);
        if (($this->recipients[$email] ?? null) === $type) {
            unset($this->recipients[$email]);
        }
    }

    /**
     * Gets all recipients and their type.
     *
     * @return array[] List of recipient and types.
     */
    public function getRecipients(): array
    {
        $recipients = [];
        foreach ($this->recipients as $recipient => $recipientType) {
            $recipients[] = [
                'type'  => $recipientType,
                'email' => $recipient,
            ];
        }

        return $recipients;
    }

    /**
     * Gets the sender email address.
     *
     * @return string The sender email address.
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * Sets the sender email address.
     *
     * @param string $emailAddress      The sender email address.
     * @throws InvalidArgumentException If the email address is empty or invalid.
     */
    public function setFrom(string $emailAddress): void
    {
        $this->from = $this->getAndCheckEmailAddress($emailAddress);
    }

    /**
     * Gets the replyTo email address.
     *
     * @return string The replyTo email address.
     */
    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    /**
     * Sets the replyTo email address.
     *
     * @param string $emailAddress The replyTo email address.
     * @throws InvalidArgumentException If the email address is empty or invalid.
     */
    public function setReplyTo(string $emailAddress): void
    {
        $this->replyTo = $this->getAndCheckEmailAddress($emailAddress);
    }

    /**
     * Gets the subject.
     *
     * @return string The email subject.
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Sets the email subject.
     *
     * @param string $subject           The Subject for this Email.
     * @throws InvalidArgumentException If the subject is empty.
     */
    public function setSubject(string $subject): void
    {
        $trimmedSubject = trim($subject);
        if ($trimmedSubject === '') {
            throw new InvalidArgumentException(
                'Subject should not be empty.'
            );
        }

        $this->subject = $trimmedSubject;
    }

    /**
     * Sets the HTML message part.
     *
     * @param HTMLMessage $messagePart The HTML message part.
     */
    public function setHTMLMessage(HTMLMessage $messagePart): void
    {
        $this->htmlMessage = $messagePart;
    }

    /**
     * Gets the HTML message part.
     *
     * @return HTMLMessage The HTML message part.
     */
    public function getHTMLMessage(): HTMLMessage
    {
        return $this->htmlMessage;
    }

    /**
     * Sets the plain-text message part.
     *
     * @param TextMessage $messagePart The PlainText message part.
     */
    public function setTextMessage(TextMessage $messagePart): void
    {
        $this->textMessage = $messagePart;
    }

    /**
     * Gets the plain-text message part.
     *
     * @return TextMessage|null The PlainText message part.
     */
    public function getTextMessage(): ?TextMessage
    {
        return $this->textMessage;
    }

    /**
     * Adds an attachment to this email.
     *
     * @param EmailAttachment $attachment The attachment to add.
     */
    public function addAttachment(EmailAttachment $attachment): void
    {
        $this->attachments[] = $attachment;
    }

    /**
     * Remove an attachment from this email.
     *
     * @param EmailAttachment $attachment The attachment to remove.
     */
    public function removeAttachment(EmailAttachment $attachment): void
    {
        $index = array_search($attachment, $this->attachments);
        if ($index !== false) {
            unset($this->attachments[$index]);
        }
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
