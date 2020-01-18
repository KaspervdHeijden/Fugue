<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Core\Runtime\HttpRuntime;
use Fugue\Core\Runtime\CLIRuntime;

use function php_sapi_name;
use function mb_strtolower;
use function mb_substr;

final class RuntimeFactory
{
    /**
     * @var string The CLI value.
     */
    private const CLI_NAME = 'cli';

    /**
     * Determines if the current request was called from the CI.
     *
     * @return bool TRUE if the request was issued from the CLI, FALSE otherwise.
     */
    private function isCalledFromCLI(): bool
    {
        if (mb_strtolower(mb_substr(php_sapi_name(), 0, 3)) === self::CLI_NAME) {
            return true;
        }

        return false;
    }

    /**
     * Gets a RuntimeInterface from a request.
     *
     * @param FrameWork $frameWork The frameWork to use.
     * @return RuntimeInterface    A RuntimeInterface suitable to handle the request.
     */
    public function getRuntime(FrameWork $frameWork): RuntimeInterface
    {
        if ($this->isCalledFromCLI()) {
            return new CLIRuntime();
        }

        return new HttpRuntime($frameWork);
    }
}
