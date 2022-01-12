<?php

namespace Spatie\ServerMonitor\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\ServerMonitor\Models\Host;

class Record extends Model
{
    protected $table = 'monitoring_records';

    public $timestamps = false;

    public $dates = [
        'created_at'
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(config('server-monitor.host_model', Host::class));
    }

    public function scopeToday($query) {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeLastWeek($query) {
        return $query->whereDate('created_at', '>=', Carbon::today()->subWeek());
    }

    public function scopeLastMonth($query) {
        return $query->whereDate('created_at', '>=', Carbon::today()->subMonth());
    }
}
