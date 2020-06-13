<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

final class TemplateAdapterFactory
{
    /** @var TemplateInterface[] */
    private array $templates;

    public function __construct(TemplateInterface ...$templateInterfaces)
    {
        $this->templates = $templateInterfaces;
    }

    /**
     * Gets a TemplateInterface.
     *
     * @param string $name       The template filename to get the TemplateInterface for.
     * @return TemplateInterface The TemplateInterface.
     */
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
