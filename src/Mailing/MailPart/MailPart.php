<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

use UnexpectedValueException;

use function quoted_printable_encode;
use function base64_encode;
use function chunk_split;

abstract class MailPart
{
    public const TRANSFER_ENCODING_QUOTED_PRINTABLE = 'quoted-printable';
    public const TRANSFER_ENCODING_BASE64           = 'base64';
    public const TRANSFER_ENCODING_7BIT             = '7bit';
    public const TRANSFER_ENCODING_NONE             = '';

    public const NEWLINE                = "\r\n";
    public const BASE64_MAX_LINE_LENGTH = 76;

    private string $transferEncoding;
    private string $contentType;
    private string $body;

    public function __construct(
        string $body,
        string $contentType,
        string $transferEncoding = self::TRANSFER_ENCODING_QUOTED_PRINTABLE
    ) {
        $this->transferEncoding = $transferEncoding;
        $this->contentType      = $contentType;
        $this->body             = $body;
    }

    final public function getContentType(): string
    {
        return $this->contentType;
    }

    final public function getTransferEncoding(): string
    {
        return $this->transferEncoding;
    }

    final public function getBody(): string
    {
        return $this->body;
    }

    public function getEncodedBody(): string
    {
        switch ($this->transferEncoding) {
            case self::TRANSFER_ENCODING_7BIT:
            case self::TRANSFER_ENCODING_NONE:
                return $this->body;
            case self::TRANSFER_ENCODING_BASE64:
                return chunk_split(
                    base64_encode($this->body),
                    self::BASE64_MAX_LINE_LENGTH,
                    self::NEWLINE
                );
            case self::TRANSFER_ENCODING_QUOTED_PRINTABLE:
                return quoted_printable_encode($this->body);
            default:
                throw new UnexpectedValueException(
                    "Unrecognized transfer encoding '{$this->transferEncoding}'"
                );
        }
    }
}
