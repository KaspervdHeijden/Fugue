<?php

declare(strict_types=1);

namespace Fugue\CronJob;

interface CronJobInterface
{
    /**
     * Calls the CronJob.
     */
    public function run(): void;
}
