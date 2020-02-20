<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\Core\Exception\FugueException;

final class InvalidTemplateException extends FugueException
{
    public static function forUnrecognizedTemplateName(string $name): self
    {
        return new static("Could not load template for '{$name}'.");
    }
}
