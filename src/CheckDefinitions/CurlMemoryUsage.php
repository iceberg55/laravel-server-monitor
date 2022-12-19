<?php

namespace Spatie\ServerMonitor\CheckDefinitions;

use Symfony\Component\Process\Process;

class CurlMemoryUsage extends CheckDefinition
{

    const NAME = 'cpu-memory';
    public $command = 'curl --silent http://localhost:50000?action=getmem';

    public function command(): string
    {
        return str_replace('localhost', $this->check->host->ip, $this->command);
    }

    public function resolve(Process $process)
    {
        $percentage = $process->getOutput();

        $thresholds = config('server-monitor.memory_usage_threshold', [
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
}
