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

    public function getForTemplate(string $name): TemplateInterface
    {
        foreach ($this->templates as $template) {
            if ($template->supports($name)) {
                return $template;
            }
        }

        throw InvalidTemplateException::forUnrecognizedTemplateName($name);
    }
}
