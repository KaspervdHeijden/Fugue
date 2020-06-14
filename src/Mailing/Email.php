<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use Fugue\Mailing\MailPart\PlainTextMessage;
use Fugue\Mailing\MailPart\HtmlTextMessage;
use Fugue\Mailing\Recipient\RecipientList;
use Fugue\Mailing\MailPart\AttachmentList;
use Fugue\Mailing\Recipient\EmailAddress;
use Fugue\Mailing\MailPart\TextMessage;
use InvalidArgumentException;

final class Email
{
    private AttachmentList $attachments;
    private RecipientList $recipients;
    private TextMessage $textMessage;
    private ?EmailAddress $replyTo = null;
    private EmailAddress $from;
    private string $subject;

    public function __construct(
        RecipientList $recipients,
        TextMessage $textMessage,
        string $subject,
        EmailAddress $from,
        ?EmailAddress $replyTo       = null,
        ?AttachmentList $attachments = null
    ) {
        if ($subject === '') {
            throw new InvalidArgumentException(
                'Subject must not be empty.'
            );
        }

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->attachments = $attachments ?: new AttachmentList();
        $this->textMessage = $textMessage;
        $this->recipients  = $recipients;
        $this->subject     = $subject;
        $this->replyTo     = $replyTo;
        $this->from        = $from;
    }

    public static function forText(
        string $to,
        string $subject,
        string $text,
        string $from
    ): self {
        return new static(
            RecipientList::forValues(new EmailAddress($to)),
            new PlainTextMessage($text),
            $subject,
            new EmailAddress($from)
        );
    }

    public static function forHtml(
        string $to,
        string $subject,
        string $text,
        string $from
    ): self {
        return new static(
            RecipientList::forValues(new EmailAddress($to)),
            new HtmlTextMessage($text),
            $subject,
            new EmailAddress($from)
        );
    }

    public function getRecipients(): RecipientList
    {
        return $this->recipients;
    }

    public function getTextMessage(): TextMessage
    {
        return $this->textMessage;
    }

    public function getFrom(): EmailAddress
    {
        return $this->from;
    }

    public function getReplyTo(): ?EmailAddress
    {
        return $this->replyTo;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getAttachments(): AttachmentList
    {
        return $this->attachments;
    }
}
