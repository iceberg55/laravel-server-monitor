<?php

namespace Spatie\ServerMonitor\CheckDefinitions;

use Spatie\ServerMonitor\CheckDefinitions\CheckDefinition;
use Symfony\Component\Process\Process;

class CPUUsage extends CheckDefinition
{
  public $command = "grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'";

  public function resolve(Process $process)
  {
    $percentage = $this->getCPUUsagePercentage($process->getOutput());

    $message = "usage at {$percentage}%";

    $thresholds = config('server-monitor.cpu_usage_threshold', [
      'warning' => 80,
      'fail' => 90,
    ]);

    if ($percentage >= $thresholds['fail']) {
      $this->check->fail($message);

      return;
    }

    if ($percentage >= $thresholds['warning']) {
      $this->check->warn($message);

      return;
    }

    $this->check->succeed($message);
  }


  protected function getCPUUsagePercentage(string $commandOutput): float
  {
    return round((float) $commandOutput, 2);
  }
}
