<?php

namespace Spatie\ServerMonitor\CheckDefinitions;

use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class MemoryUsage extends CheckDefinition
{
  public $command = "cat /proc/meminfo";
  const NAME = 'memory';

  public function resolve(Process $process)
  {
    $percentage = $this->getMemoryUsage($process->getOutput());

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

  protected function getMemoryUsage(string $commandOutput): float
  {
    preg_match_all('/(\d+)/', $commandOutput, $pieces);

    $used = round($pieces[0][6] / $pieces[0][0], 2);

    return $used;
  }
}