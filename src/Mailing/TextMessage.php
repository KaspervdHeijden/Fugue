<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use Fugue\HTTP\Response;

final class TextMessage extends MailPart
{
    public function getTransferEncoding(): string
    {
        return MailPart::TRANSFER_ENCODING_QUOTED_PRINTABLE;
    }

    public function getContentType(): string
    {
        return Response::CONTENT_TYPE_PLAINTEXT;
    }
}
