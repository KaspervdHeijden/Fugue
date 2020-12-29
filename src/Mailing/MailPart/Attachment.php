<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

final class Attachment extends MailPart
{
    public const DISPOSITION_ATTACHMENT = 'attachment';
    public const DISPOSITION_INLINE     = 'inline';

    private string $disposition;
    private string $fileName;

    public function __construct(
        string $body,
        string $contentType,
        string $fileName         = '',
        string $transferEncoding = MailPart::TRANSFER_ENCODING_BASE64,
        string $disposition      = self::DISPOSITION_ATTACHMENT
    ) {
        parent::__construct(
            $body,
            $contentType,
            $transferEncoding
        );

        $this->disposition = $disposition;
        $this->fileName    = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getDisposition(): string
    {
        return $this->disposition;
    }

    public function isAttachment(): bool
    {
        return ($this->disposition === self::DISPOSITION_ATTACHMENT);
    }

    public function isInline(): bool
    {
        return ($this->disposition === self::DISPOSITION_INLINE);
    }
}
