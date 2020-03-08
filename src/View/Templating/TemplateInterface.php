<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\Collection\PropertyBag;

interface TemplateInterface
{
    /**
     * Renders a template file, and returns the output as a string.
     *
     * @param string      $templateName The template filename to fetch.
     * @param PropertyBag $variables    The variables passed to the template.
     *
     * @return string                   The generated content from the template.
     */
    public function render(string $templateName, PropertyBag $variables): string;

    /**
     * Gets a value if an implementation excepts and supports a template.
     *
     * @return bool TRUE if the implementation supports the given template,
     *              FALSE otherwise.
     */
    public function supports(string $name): bool;
}
