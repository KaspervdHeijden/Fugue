<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

final class Attachment extends MailPart
{
    /** @var string */
    public const DISPOSITION_ATTACHMENT = 'attachment';

    /** @var string */
    public const DISPOSITION_INLINE = 'inline';

    /** @var string */
    private $disposition;

    /** @var string */
    private $fileName;

    /**
     * Instantiates an EmailAttachment.
     *
     * @param string $body             The content of the attachment.
     * @param string $contentType      The content type of the attachment.
     * @param string $fileName         The filename of the attachment.
     * @param string $transferEncoding The transfer encoding of the attachment.
     * @param string $disposition      The disposition type. One of the DISPOSITION_* constants.
     */
    public function __construct(
        string $body,
        string $contentType,
        string $fileName         = '',
        string $transferEncoding = MailPart::TRANSFER_ENCODING_BASE64,
        string $disposition      = self::DISPOSITION_ATTACHMENT
    ) {
        parent::__construct($body, $contentType, $transferEncoding);

        $this->disposition      = $disposition;
        $this->fileName         = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getDisposition(): string
    {
        return $this->disposition;
    }
}
