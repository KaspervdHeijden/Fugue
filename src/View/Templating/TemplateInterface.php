<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

interface TemplateInterface
{
    /**
     * Renders a template file, and returns the output as a string.
     *
     * @param string $templateName The template filename to fetch.
     * @param array  $variables    The variables passed to the template.
     *
     * @return string              The generated content from the template.
     */
    public function render(string $templateName, array $variables): string;
}
