<?php

namespace Spatie\ServerMonitor;

use Illuminate\Database\Eloquent\Builder;
use Spatie\ServerMonitor\Exceptions\InvalidConfiguration;
use Spatie\ServerMonitor\Models\Check;

class CheckRepository
{
    public static function allThatShouldRun(): CheckCollection
    {
        $checks = self::query()->get()->filter->shouldRun();

        return new CheckCollection($checks);
    }

    protected static function query(): Builder
    {
        $modelClass = static::determineCheckModel();

        return $modelClass::enabled();
    }

    public static function determineCheckModel(): string
    {
        $monitorModel = config('server-monitor.check_model') ?? Check::class;

        if (! is_a($monitorModel, Check::class, true)) {
            throw InvalidConfiguration::checkModelIsNotValid($monitorModel);
        }

        return $monitorModel;
    }

    public static function getAllNames(): array
    {
        $checks = config('server-monitor.checks');

        return collect($checks)->map(function($check) {
            return $check::NAME;
        })->toArray();
    }

}
