<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

use Fugue\HTTP\Response;

final class PlainTextMessage extends TextMessage
{
    public function __construct(
        string $body,
        string $transferEncoding = self::TRANSFER_ENCODING_QUOTED_PRINTABLE
    ) {
        parent::__construct(
            $body,
            Response::CONTENT_TYPE_PLAINTEXT,
            $transferEncoding
        );
    }
}
