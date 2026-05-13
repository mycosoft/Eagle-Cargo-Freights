<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            ActivityLog::logCreated(
                $model,
                request()->ip(),
                request()->userAgent()
            );
        });

        static::updated(function (Model $model) {
            if ($model->isDirty()) {
                ActivityLog::logUpdated(
                    $model,
                    $model->getOriginal(),
                    request()->ip(),
                    request()->userAgent()
                );
            }
        });

        static::deleted(function (Model $model) {
            ActivityLog::logDeleted(
                $model,
                $model->getOriginal(),
                request()->ip(),
                request()->userAgent()
            );
        });
    }

    public function activityLogs()
    {
        return ActivityLog::where('model_type', static::class)
            ->where('model_id', $this->getKey())
            ->orderBy('created_at', 'desc');
    }
}