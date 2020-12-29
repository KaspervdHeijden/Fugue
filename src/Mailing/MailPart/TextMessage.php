<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

abstract class TextMessage extends MailPart
{
    protected const DEFAULT_LINE_LENGTH = 32;
}
