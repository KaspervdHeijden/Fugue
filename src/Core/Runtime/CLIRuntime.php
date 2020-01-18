<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\CronJob\CronJobFactory;
use InvalidArgumentException;
use Fugue\Core\FrameWork;
use Fugue\HTTP\Request;

use function array_slice;
use function count;

final class CLIRuntime implements RuntimeInterface
{
    /** @var FrameWork */
    private $framework;

    public function __construct(FrameWork $frameWork)
    {
        $this->framework = $frameWork;
    }

    public function handle(Request $request): void
    {
        $arguments = $request->server()->getArray('argv');
        if (count($arguments) < 2) {
            throw new InvalidArgumentException('No cron given.');
        }

        $cronArgs = array_slice($arguments, 2);
        $cronName = $arguments[1];

        $factory  = new CronJobFactory($this->framework->getConfig());
        $cronJob  = $factory->getCronJobFromIdentifier($cronName);

        $cronJob->run($cronArgs);
    }
}
