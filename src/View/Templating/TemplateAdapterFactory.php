<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

final class TemplateAdapterFactory
{
    private array $templates;

    public function __construct(TemplateInterface ...$templateInterfaces)
    {
        $this->templates = $templateInterfaces;
    }

    public function getForTemplateName(string $templateName): TemplateInterface
    {
        foreach ($this->templates as $template) {
            if ($template->supports($templateName)) {
                return $template;
            }
        }

        throw InvalidTemplateException::forUnrecognizedTemplateName($templateName);
    }
}
