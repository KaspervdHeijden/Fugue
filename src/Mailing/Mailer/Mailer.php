<?php

declare(strict_types=1);

namespace Fugue\Mailing\Mailer;

use Fugue\Mailing\MailPart\HtmlTextMessage;
use Fugue\Mailing\MailPart\AttachmentList;
use Fugue\Mailing\Recipient\BccRecipient;
use Fugue\Mailing\Recipient\EmailAddress;
use Fugue\Mailing\Recipient\ToRecipient;
use Fugue\Mailing\Recipient\CcRecipient;
use Fugue\Mailing\MailPart\TextMessage;
use Fugue\Mailing\Recipient\Recipient;
use Fugue\Mailing\MailPart\Attachment;
use Fugue\Mailing\MailPart\MailPart;
use Fugue\Collection\CollectionMap;
use Fugue\Mailing\Email;

use function array_filter;
use function array_push;
use function implode;
use function uniqid;

abstract class Mailer implements EmailSenderInterface
{
    /** @var string */
    public const BOUNDARY_PREFIX = 'np';

    /** @var string */
    public const MIME_VERSION = '1.0';

    /**
     * Generates a boundary.
     *
     * @return string A boundary identifier.
     */
    private function generateBoundary(): string
    {
        return uniqid(self::BOUNDARY_PREFIX, false);
    }

    /**
     * Generates the top-level headers for an email.
     *
     * @param Email  $email    The email to generate the top-level headers for.
     * @param string $boundary The top-level boundary identifier.
     *
     * @return array           Key/Value pairs of headers.
     */
    private function getHeaders(Email $email, string $boundary): array
    {
        $replyTo = $email->getReplyTo();
        $from    = $email->getFrom();

        if (! $replyTo instanceof EmailAddress) {
            $replyTo = $from;
        }

        $headers = [
            'Cc'           => $this->recipientListToString($email, CcRecipient::class),
            'Bcc'          => $this->recipientListToString($email, BccRecipient::class),
            'MIME-Version' => self::MIME_VERSION,
            'Reply-To'     => $replyTo->getEmailAddress(),
            'From'         => $from->getEmailAddress(),
            'Return-Path'  => $from->getEmailAddress(),
            'Content-Type' => "multipart/mixed; boundary=\"{$boundary}\"",
        ];

        return array_filter($headers);
    }

    /**
     * Generates a message body.
     *
     * @param TextMessage    $textPart    The message.
     * @param string         $boundary    The top-level boundary identifier.
     * @param AttachmentList $attachments The attachments.
     *
     * @return string                     The message body.
     */
    private function getBody(
        TextMessage $textPart,
        string $boundary,
        AttachmentList $attachments
    ): string {
        $contentBoundary = $this->generateBoundary();
        $body            = [
            "--{$boundary}",
            "Content-Type: multipart/alternative; boundary=\"{$contentBoundary}\"",
            '',
        ];

        $htmlPart = null;
        if ($textPart instanceof HtmlTextMessage) {
            $htmlPart = $textPart;
            $textPart = $textPart->generatePlainTextMessage();
        }

        foreach ([$textPart, $htmlPart] as $part) {
            if (! $textPart instanceof TextMessage) {
                continue;
            }

            array_push(
                $body,
                "--{$contentBoundary}",
                "Content-Type: {$part->getContentType()}",
                "Content-Transfer-Encoding: {$part->getTransferEncoding()}",
                '',
                $part->getEncodedBody(),
                ''
            );
        }

        array_push($body, "--{$contentBoundary}--", '');
        foreach ($attachments as $attachment) {
            $contentType = $attachment->getContentType();
            $disposition = $attachment->getDisposition();
            $fileName    = $attachment->getFileName();

            if ($fileName !== '') {
                $contentType .= "; name=\"{$fileName}\"";

                if ($disposition === Attachment::DISPOSITION_ATTACHMENT) {
                    $disposition .= "; filename=\"{$fileName}\"";
                }
            }

            array_push(
                $body,
                "--{$boundary}",
                "Content-Type: {$contentType}",
                "Content-Transfer-Encoding: {$attachment->getTransferEncoding()}",
                "Content-Disposition: {$disposition}",
                '',
                $attachment->getEncodedBody(),
                ''
            );
        }

        $body[] = "--{$boundary}--";
        return implode(MailPart::NEWLINE, $body);
    }

    /**
     * Sends an email.
     *
     * @param Email $email The email to send.
     */
    public function send(Email $email): void
    {
        $boundary = $this->generateBoundary();
        $textPart = $email->getTextMessage();
        $headers  = implode(MailPart::NEWLINE, $this->getHeaders($email, $boundary));
        $body     = $this->getBody($textPart, $boundary, $email->getAttachments());

        $this->sendMail(
            $this->recipientListToString($email, ToRecipient::class),
            $email->getSubject(),
            $body,
            trim($headers)
        );
    }

    private function recipientListToString(Email $email, string $class): string
    {
        /** @var CollectionMap $emailAddresses */
        $emailAddresses = $email->getRecipients()->reduce(
            static function (CollectionMap $carry, Recipient $recipient) use ($class): CollectionMap {
                if ($recipient instanceof $class) {
                    $carry[$recipient->getEmailAddress()->getEmailAddress()] = 1;
                }

                return $carry;
            },
            new CollectionMap([], 'int')
        );

        return implode(', ', $emailAddresses->keys());
    }

    /**
     * Implementation dependant method responsible for sending the email.
     *
     * @param string $to      The email recipient(s).
     * @param string $subject The email subject.
     * @param string $body    The email body.
     * @param string $headers The additional top-level headers.
     */
    abstract protected function sendMail(
        string $to,
        string $subject,
        string $body,
        string $headers
    ): void;
}
