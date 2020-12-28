<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\Collection\PropertyBag;

interface TemplateInterface
{
    public function render(string $templateName, PropertyBag $variables): string;

    public function supports(string $templateName): bool;
}
