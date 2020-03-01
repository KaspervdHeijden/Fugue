<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use UnexpectedValueException;

use function quoted_printable_encode;
use function base64_encode;
use function chunk_split;

abstract class MailPart
{
    /**
     * @var string The quoted printable body encoding.
     */
    public const TRANSFER_ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

    /**
     * @var string The base64 body encoding.
     */
    public const TRANSFER_ENCODING_BASE64 = 'base64';

    /**
     * @var string The 7bit body encoding.
     */
    public const TRANSFER_ENCODING_7BIT = '7bit';

    /**
     * @var string No transfer encoding.
     */
    public const TRANSFER_ENCODING_NONE = '';

    /**
     * @var int The maximum line length of an 64 encoded body.
     */
    public const BASE64_MAX_LINE_LENGTH = 76;

    /**
     * @var string Newline sequence for an email body.
     */
    public const NEWLINE = "\r\n";

    /** @var string */
    private $body = '';

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * Gets the content type of this mail part.
     *
     * @return string The content type.
     */
    abstract public function getContentType(): string;

    /**
     * Gets the encoding type of this mail part.
     *
     * @return string The encoding type.
     */
    abstract public function getTransferEncoding(): string;

    final public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Returns the body, encoded by the transfer encoding.
     *
     * @return string The encoded body.
     */
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
                    "Unrecognized transfer encoding '{$this->transferEncoding}'."
                );
        }
    }
}
