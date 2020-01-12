<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use UnexpectedValueException;
use RuntimeException;

use function array_merge;
use function array_push;
use function implode;
use function uniqid;
use function count;

abstract class MailerService implements EmailSenderInterface
{
    /** @var string */
    public const BOUNDARY_PREFIX = 'np';

    /** @var string */
    public const MIME_VERSION    = '1.0';

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
     * @param Email  $email     The email to generate the top-level headers for.
     * @param string $boundary  The top-level boundary identifier.
     *
     * @return array            Key/Value pairs of headers.
     */
    private function getHeaders(Email $email, string $boundary): array
    {
        $from = $email->getFrom();
        if ($from === '') {
            throw new UnexpectedValueException('No sender email address specified.');
        }

        $replyTo = $email->getReplyTo();
        if ($replyTo === '') {
            $replyTo = $from;
        }

        $subject = $email->getSubject();
        if ($subject === '') {
            throw new UnexpectedValueException('No subject specified.');
        }

        $recipients = [
            'bcc' => [],
            'cc'  => [],
            'to'  => [],
        ];

        foreach ($email->getRecipients() as $recipient) {
            switch ($recipient['type']) {
                case Email::RECIPIENT_TYPE_TO:
                    $recipients['to'][] = $recipient['email'];
                    break;
                case Email::RECIPIENT_TYPE_CC:
                    $recipients['cc'][] = $recipient['email'];
                    break;
                case Email::RECIPIENT_TYPE_BCC:
                    $recipients['bcc'][] = $recipient['email'];
                    break;
                default:
                    throw new UnexpectedValueException(
                        "Unrecognised recipient type ({$recipient['type']})."
                    );
            }
        }

        if (count($recipients['to']) === 0) {
            throw new RuntimeException('No recipients specified.');
        }

        $headers = [
            'To'           => implode(', ', $recipients['to']),
            'MIME-Version' => self::MIME_VERSION,
            'From'         => $from,
            'Subject'      => $subject,
            'Reply-To'     => $replyTo,
            'Return-Path'  => $from,
        ];

        if (count($recipients['cc']) > 0) {
            $headers['Cc'] = implode(', ', $recipients['cc']);
        }

        if (count($recipients['bcc']) > 0) {
            $headers['Bcc'] = implode(', ', $recipients['bcc']);
        }

        return array_merge(
            $headers,
            ['Content-Type' => "multipart/mixed; boundary=\"{$boundary}\""]
        );
    }

    /**
     * Generates a message body.
     *
     * @param TextMessage       $textPart    The PlainText message part.
     * @param HTMLMessage       $htmlPart    The HTML message part.
     * @param string            $boundary    The top-level boundary identifier.
     * @param EmailAttachment[] $attachments The attachments.
     * @return string                        The message body.
     */
    private function getBody(
        ?TextMessage $textPart = null,
        ?HTMLMessage $htmlPart = null,
        string $boundary = '',
        array $attachments = []
    ): string {
        $contentBoundary = $this->generateBoundary();
        $body            = [
            "--{$boundary}",
            "Content-Type: multipart/alternative; boundary=\"{$contentBoundary}\"",
            '',
        ];

        foreach ([$textPart, $htmlPart] as $part) {
            array_push($body,
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

                if ($disposition === EmailAttachment::DISPOSITION_ATTACHMENT) {
                    $disposition .= "; filename=\"{$fileName}\"";
                }
            }

            array_push(
                $body,
                "--{$boundary}",
                "Content-Type: {$contentType}",
                "Content-Transfer-Encoding: " . $attachment->getTransferEncoding(),
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
        $htmlPart = $email->getHTMLMessage();
        $textPart = $email->getTextMessage();

        if ($htmlPart instanceof HTMLMessage && ! $textPart instanceof TextMessage) {
            $textPart = $htmlPart->generateTextPart();
        }

        if (! $textPart instanceof TextMessage && ! $htmlPart instanceof HTMLMessage) {
            throw new UnexpectedValueException('No message part defined.');
        }

        $boundary    = $this->generateBoundary();
        $attachments = $email->getAttachments();
        $headersList = $this->getHeaders($email, $boundary);
        $subject     = $headersList['Subject'];
        $mailTo      = $headersList['To'];
        $headers     = '';

        foreach ($headersList as $name => $value) {
            if ($name !== 'To' && $name !== 'Subject') {
                $headers .= "{$name}: {$value}" . MailPart::NEWLINE;
            }
        }

        $body = $this->getBody($textPart, $htmlPart, $boundary, $attachments);
        $this->sendMail($mailTo, $subject, $body, trim($headers));
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
