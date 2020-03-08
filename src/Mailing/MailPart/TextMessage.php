<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

abstract class TextMessage extends MailPart
{
    /**
     * @var int Default line length for HR elements.
     */
    protected const DEFAULT_LINE_LENGTH = 32;
}
