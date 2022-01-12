<?php

namespace Spatie\ServerMonitor\Manipulators;

use Spatie\ServerMonitor\Manipulators\Manipulator;
use Spatie\ServerMonitor\Models\Check;
use Symfony\Component\Process\Process;


class ManipulatorLocal implements Manipulator
{
    public function manipulateProcess(Process $process, Check $check): Process
    {
        $process = Process::fromShellCommandline($check->getDefinition()->command());

        return $process;
    }
}