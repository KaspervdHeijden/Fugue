<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use InvalidArgumentException;

use function preg_match;

final class TemplateAdapterFactory
{
    /** @var TemplateUtil */
    private $templateUtil;

    public function __construct(TemplateUtil $templateUtil)
    {
        $this->templateUtil = $templateUtil;
    }

    /**
     * Gets a TemplateInterface.
     *
     * @param string $template   The template filename to get the TemplateInterface for.
     * @return TemplateInterface The TemplateInterface.
     */
    public function getForTemplate(string $template): TemplateInterface
    {
        if ((bool)preg_match('/\.php$/', $template)) {
            return new PHPTemplateAdapter($this->templateUtil);
        }

        throw new InvalidArgumentException(
            "Identifier {$template} not recognized."
        );
    }
}
