<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use InvalidArgumentException;

use function preg_match;
use function realpath;
use function rtrim;

final class TemplateAdapterFactory
{
    /** @var string */
    private const TEMPLATES_SUBDIRECTORY = 'templates/';

    /** @var TemplateUtil */
    private $templateUtil;

    public function __construct(TemplateUtil $templateUtil)
    {
        $this->templateUtil = $templateUtil;
    }

    private function getTemplateDirectory(): string
    {
        $rootDir = rtrim(realpath(__DIR__ . '/../../../'), '/');
        return "{$rootDir}/" . self::TEMPLATES_SUBDIRECTORY;
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
                $this->getTemplateDirectory(),
                $this->templateUtil
            );
        }

        throw new InvalidArgumentException(
            "Identifier {$template} not recognized."
        );
    }
}
