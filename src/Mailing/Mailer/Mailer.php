<?php

declare(strict_types=1);

namespace Fugue\Mailing\Mailer;

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
            'Bcc'          => $this->recipientListToString($email, BccRecipient::class),
            'Cc'           => $this->recipientListToString($email, CcRecipient::class),
            'Content-Type' => "multipart/mixed; boundary=\"{$boundary}\"",
            'Reply-To'     => $replyTo->getEmailAddress(),
            'From'         => $from->getEmailAddress(),
            'Return-Path'  => $from->getEmailAddress(),
            'MIME-Version' => self::MIME_VERSION,
        ];

        return array_filter($headers);
    }

    /**
     * Generates a message body.
     *
     * @param Email   $email    The Email.
     * @param string  $boundary The top-level boundary identifier.
     *
     * @return string The message body.
     */
    private function getBody(Email $email, string $boundary): string
    {
        $contentBoundary = $this->generateBoundary();
        $body            = [
            "--{$boundary}",
            "Content-Type: multipart/alternative; boundary=\"{$contentBoundary}\"",
            '',
        ];

        foreach ($email->getMailParts() as $part) {
            if (! $part instanceof TextMessage) {
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
        /** @var Attachment $attachment */
        foreach ($email->getAttachments() as $attachment) {
            $contentType = $attachment->getContentType();
            $disposition = $attachment->getDisposition();
            $fileName    = $attachment->getFileName();

            if ($fileName !== '') {
                $contentType .= "; name=\"{$fileName}\"";

                if ($attachment->isAttachment()) {
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
        $headers  = implode(MailPart::NEWLINE, $this->getHeaders($email, $boundary));
        $body     = $this->getBody($email, $boundary);

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
