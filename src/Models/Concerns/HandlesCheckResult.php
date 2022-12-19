<?php

namespace Spatie\ServerMonitor\Models\Concerns;

use Spatie\ServerMonitor\Events\CheckFailed;
use Spatie\ServerMonitor\Events\CheckSucceeded;
use Spatie\ServerMonitor\Events\CheckWarning;
use Spatie\ServerMonitor\Helpers\ConsoleOutput;
use Spatie\ServerMonitor\Models\Enums\CheckStatus;
use Spatie\ServerMonitor\Models\Record;

trait HandlesCheckResult
{
    private function saveRecord($value) {
        $class = config('server-monitor.record_model', Record::class);
        $record = new $class;
        $record->vps_id = $this->host->id;
        $record->value = $value;
        $record->type = $this->type;
        $record->status = $this->status;
        $record->created_at = now();

        $record->save();
    }

    public function succeed(string $message = '')
    {
        $this->status = CheckStatus::SUCCESS;
        $this->last_run_value = $message;

        $this->save();

        $this->saveRecord($message);

        event(new CheckSucceeded($this));
        ConsoleOutput::info($this->host->name.": check `{$this->type}` succeeded");

        return $this;
    }

    public function warn(string $value = '')
    {
        $this->status = CheckStatus::WARNING;
        $this->last_run_value = $value;

        $this->save();

        $this->saveRecord($value);

        event(new CheckWarning($this));

        ConsoleOutput::info($this->host->name.": check `{$this->type}` issued warning");

        return $this;
    }

    public function fail(string $value = '')
    {
        $this->status = CheckStatus::FAILED;
        $this->last_run_value = $value;

        $this->save();
        $this->saveRecord($value);

        event(new CheckFailed($this));

        ConsoleOutput::error($this->host->name.": check `{$this->type}` failed");

        return $this;
    }
}
