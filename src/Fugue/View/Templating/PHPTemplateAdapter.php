<?php

declare(strict_types=1);

namespace Fugue\View\Templating;

use Fugue\IO\Filesystem\FileSystemInterface;
use Fugue\HTTP\Routing\RouteMatcher;
use Fugue\Collection\PropertyBag;

use function htmlspecialchars;
use function ob_get_clean;
use function preg_match;
use function is_string;
use function ob_start;
use function extract;

use const DIRECTORY_SEPARATOR;
use const ENT_QUOTES;
use const ENT_HTML5;

final class PHPTemplateAdapter implements TemplateInterface
{
    private FileSystemInterface $fileSystem;
    private RouteMatcher $matcher;
    private string $rootDir;

    public function __construct(
        RouteMatcher $matcher,
        FileSystemInterface $fileSystem,
        string $rootDir
    ) {
        $this->fileSystem = $fileSystem;
        $this->matcher    = $matcher;
        $this->rootDir    = $rootDir;
    }

    public function escape(mixed $text, int $flags = ENT_HTML5 | ENT_QUOTES): string
    {
        return htmlspecialchars((string)$text, $flags);
    }

    public function route(string $routeName, array $parameters = []): string
    {
        return $this->matcher->getUrl($routeName, $parameters);
    }

    private function getFilename(string $templateName): string
    {
        return $this->rootDir . DIRECTORY_SEPARATOR . $templateName;
    }

    public function supports(string $templateName): bool
    {
        if (! (bool)preg_match('/\.php$/', $templateName)) {
            return false;
        }

        $fileName = $this->getFilename($templateName);
        if (! $this->fileSystem->isReadableFile($fileName)) {
            return false;
        }

        return true;
    }

    public function render(string $templateName, PropertyBag $variables): string
    {
        if (! $this->supports($templateName)) {
            throw InvalidTemplateException::forUnrecognizedTemplateName($templateName);
        }

        $variableMap = array_merge($variables->toArray(), ['view' => $this]);
        $fileName    = $this->getFilename($templateName);

        return (static function (
            string $currentTemplateFileName,
            array $templateVariables
        ): string {
            ob_start();

            extract($templateVariables);
            unset($templateVariables);

            /** @noinspection PhpIncludeInspection */
            include $currentTemplateFileName;

            $content = ob_get_clean();
            if (! is_string($content)) {
                return '';
            }

            return $content;
        })($fileName, $variableMap);
    }
}
