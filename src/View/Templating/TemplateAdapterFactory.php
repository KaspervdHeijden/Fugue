<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use InvalidArgumentException;

final class TemplateAdapterFactory
{
    /** @var string */
    private const SUBDIRECTORY_PHP = 'php/';

    /** @var string */
    private $templateRootDir;

    /** @var TemplateUtil */
    private $templateUtil;

    public function __construct(TemplateUtil $templateUtil, string $templateRootDir)
    {
        $this->templateRootDir = $templateRootDir;
        $this->templateUtil    = $templateUtil;
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
            return new PHPTemplateAdapter(
                $this->templateUtil,
                $this->templateRootDir . self::SUBDIRECTORY_PHP
            );
        }

        throw new InvalidArgumentException(
            "Identifier {$template} not recognized."
        );
    }
}
