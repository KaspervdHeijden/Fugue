<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use LogicException;

use function ob_get_clean;
use function array_merge;
use function is_readable;
use function preg_match;
use function is_string;
use function ob_start;
use function extract;
use function is_file;

final class PHPTemplateAdapter implements TemplateInterface
{
    /** @var string */
    private const SUBDIRECTORY_PHP = 'php/';

    /** @var TemplateUtil */
    private $templateUtil;

    /** @var string */
    private $rootDir;

    public function __construct(string $rootDir, TemplateUtil $templateUtil)
    {
        $this->rootDir      = $rootDir . self::SUBDIRECTORY_PHP;
        $this->templateUtil = $templateUtil;
    }

    /**
     * Adds an extension to the template file, if missing.
     *
     * @param string $fileName The template filename to get the full path for.
     * @return string          The full path to the template.
     */
    private function getFullPath(string $fileName): string
    {
        if ($fileName === '') {
            throw new LogicException('Template filename should not be empty.');
        }

        $fullPath = "{$this->rootDir}/{$fileName}";
        if (! (bool)preg_match('/\.php$/', $fullPath)) {
            $fullPath .= '.php';
        }

        if (! is_file($fullPath) || ! is_readable($fullPath)) {
            throw new LogicException("Template file not found: '{$fullPath}'.");
        }

        return $fullPath;
    }

    public function render(string $templateName, array $variables): string
    {
        $variables        = array_merge($variables, ['view' => $this->templateUtil]);
        $fullTemplatePath = $this->getFullPath($templateName);

        return (static function (string $templateFileName, array $templateVariables): string {
            ob_start();

            extract($templateVariables);
            unset($templateVariables);

            /** @noinspection PhpIncludeInspection */
            include $templateFileName;

            $content = ob_get_clean();
            if (! is_string($content)) {
                return '';
            }

            return $content;
        })($fullTemplatePath, $variables);
    }
}
