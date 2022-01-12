<?php

namespace Spatie\ServerMonitor;

use Illuminate\Database\Eloquent\Builder;
use Spatie\ServerMonitor\Exceptions\InvalidConfiguration;
use Spatie\ServerMonitor\Models\Record;

class RecordRepository
{
    protected static function query(): Builder
    {
        $modelClass = static::determineRecordModel();

        return $modelClass::query();
    }

    /**
     * Determine the record model class name.
     *
     * @return string
     *
     * @throws \Spatie\ServerMonitor\Exceptions\InvalidConfiguration
     */
    public static function determineRecordModel(): string
    {
        $recordModel = config('server-monitor.record_model') ?? record::class;

        if (! is_a($recordModel, record::class, true)) {
            throw InvalidConfiguration::recordModelIsNotValid($recordModel);
        }

        return $recordModel;
    }

    public static function clean() {
        Record::where('created_at', '<', now()->subDays(config('server-monitor.purge'))->format('Y-m-d H:i:s'))->delete();
    }
}
