<?php

namespace Spatie\ServerMonitor\CheckDefinitions;

use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class CPUUsage extends CheckDefinition
{
  const NAME = 'cpu';

  public $command = "grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'";

  public function resolve(Process $process)
  {
    $percentage = $this->getCPUUsagePercentage($process->getOutput());

    $thresholds = config('server-monitor.cpu_usage_threshold', [
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


  protected function getCPUUsagePercentage(string $commandOutput): float
  {
    return round((float) $commandOutput, 2);
  }
}
