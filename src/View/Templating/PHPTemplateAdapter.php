<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use LogicException;

use function ob_get_clean;
use function array_merge;
use function is_readable;
use function mb_substr;
use function mb_strlen;
use function is_string;
use function ob_start;
use function extract;
use function is_file;

final class PHPTemplateAdapter implements TemplateInterface
{
    /** @var string */
    private const FILE_EXTENSION = '.php';

    /** @var string */
    private $templateRootDirectory;

    /** @var TemplateUtil */
    private $templateUtil;

    public function __construct(TemplateUtil $templateUtil, string $templateRootDirectory)
    {
        $this->templateRootDirectory = $templateRootDirectory;
        $this->templateUtil          = $templateUtil;
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

        $fullPath = "{$this->templateRootDirectory}/{$fileName}";
        if (mb_substr($fullPath, -mb_strlen(self::FILE_EXTENSION)) !== self::FILE_EXTENSION) {
            $fullPath .= self::FILE_EXTENSION;
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
