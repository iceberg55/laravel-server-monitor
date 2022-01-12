<?php

namespace Spatie\ServerMonitor\CheckDefinitions;

use Spatie\Regex\Regex;
use Symfony\Component\Process\Process;

class Diskspace extends CheckDefinition
{
    public $command = 'df -P .';
    const NAME = 'diskspace';

    public function resolve(Process $process)
    {
        $percentage = $this->getDiskUsagePercentage($process->getOutput());

        $thresholds = config('server-monitor.diskspace_percentage_threshold', [
            'warning' => 80,
            'fail' => 90,
        ]);

        if ($percentage >= $thresholds['fail']) {
            $this->check->fail($percentage);

            return;
        }

        if ($percentage >= $thresholds['warning']) {
            $this->check->warn($percentage);

            return;
        }

        $this->check->succeed($percentage);
    }

    protected function getDiskUsagePercentage(string $commandOutput): int
    {
        return (int) Regex::match('/(\d?\d)%/', $commandOutput)->group(1);
    }
}
