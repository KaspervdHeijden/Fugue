<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use Fugue\Mailing\MailPart\PlainTextMessage;
use Fugue\Mailing\MailPart\HtmlTextMessage;
use Fugue\Mailing\Recipient\RecipientList;
use Fugue\Mailing\MailPart\AttachmentList;
use Fugue\Mailing\Recipient\EmailAddress;
use Fugue\Mailing\MailPart\MailPartList;
use Fugue\Mailing\Recipient\ToRecipient;
use InvalidArgumentException;

final class Email
{
    private AttachmentList $attachments;
    private RecipientList $recipients;
    private ?EmailAddress $replyTo = null;
    private MailPartList $mailParts;
    private EmailAddress $from;
    private string $subject;

    public function __construct(
        RecipientList $recipients,
        MailPartList $mailParts,
        string $subject,
        EmailAddress $from,
        ?EmailAddress $replyTo,
        AttachmentList $attachments
    ) {
        if ($subject === '') {
            throw new InvalidArgumentException(
                'Subject must not be empty'
            );
        }

        $this->attachments = $attachments;
        $this->recipients  = $recipients;
        $this->mailParts   = $mailParts;
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
        return new Email(
            RecipientList::forValues(new ToRecipient(new EmailAddress($to))),
            new MailPartList([new PlainTextMessage($text)]),
            $subject,
            new EmailAddress($from),
            null,
            new AttachmentList()
        );
    }

    public static function forHtml(
        string $to,
        string $subject,
        string $text,
        string $from
    ): self {
        return new Email(
            RecipientList::forValues(new ToRecipient(new EmailAddress($to))),
            new MailPartList([new HtmlTextMessage($text)]),
            $subject,
            new EmailAddress($from),
            null,
            new AttachmentList()
        );
    }

    public function getRecipients(): RecipientList
    {
        return $this->recipients;
    }

    public function getMailParts(): MailPartList
    {
        return $this->mailParts;
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
