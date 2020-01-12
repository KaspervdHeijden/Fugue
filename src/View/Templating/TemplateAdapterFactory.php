<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\Configuration\Config;
use InvalidArgumentException;

final class TemplateAdapterFactory
{
    /** @var string */
    private const SUBDIRECTORY_PHP = 'php/';

    /**
     * @var string The PHP native template renderer.
     */
    public const TEMPLATE_PHP      = 'php';

    /**
     * @var string The system default template renderer.
     */
    public const TEMPLATE_DEFAULT = 'default';

    /** @var TemplateUtil */
    private $templateUtil;

    /** @var Config */
    private $config;

    public function __construct(TemplateUtil $templateUtil, Config $config)
    {
        $this->templateUtil = $templateUtil;
        $this->config       = $config;
    }

    /**
     * Gets a TemplateInterface.
     *
     * @param string $identifier        The identifier to get the TemplateInterface for. Defaults to system default.
     *
     * @return TemplateInterface        The TemplateInterface.
     * @throws InvalidArgumentException If the identifier could not be recognized.
     */
    public function getTemplateAdapterFromIdentifier(string $identifier = self::TEMPLATE_DEFAULT): TemplateInterface
    {
        switch ($identifier) {
            case self::TEMPLATE_PHP:
                return new PHPTemplateAdapter(
                    $this->templateUtil,
                    $this->config->getValue('directory.templates') . self::SUBDIRECTORY_PHP
                );
            case self::TEMPLATE_DEFAULT:
                return $this->getTemplateAdapterFromIdentifier(
                    $this->config->getValue('templating.identifier')
                );
            default:
                throw new InvalidArgumentException(
                    "Identifier {$identifier} not recognized."
                );
        }
    }
}
