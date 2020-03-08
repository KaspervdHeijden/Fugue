<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

use Fugue\Collection\CollectionList;

final class AttachmentList extends CollectionList
{
    protected function checkValue($value): bool
    {
        return $value instanceof Attachment;
    }
}
