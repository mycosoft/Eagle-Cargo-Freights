<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'batch_uuid',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        $class = $this->model_type;
        return $this->belongsTo($class, 'model_id');
    }

    public static function logCreated(Model $model, ?string $ip = null, ?string $userAgent = null): self
    {
        return static::createLog('created', $model, null, $model->getAttributes(), $ip, $userAgent);
    }

    public static function logUpdated(Model $model, array $oldValues, ?string $ip = null, ?string $userAgent = null): self
    {
        return static::createLog('updated', $model, $oldValues, $model->getChanges(), $ip, $userAgent);
    }

    public static function logDeleted(Model $model, array $oldValues, ?string $ip = null, ?string $userAgent = null): self
    {
        return static::createLog('deleted', $model, $oldValues, null, $ip, $userAgent);
    }

    protected static function createLog(
        string $action,
        Model $model,
        ?array $oldValues,
        ?array $newValues,
        ?string $ip,
        ?string $userAgent
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'batch_uuid' => request()->header('X-Batch-Uuid'),
        ]);
    }

    public static function getModelLabel(string $modelType): string
    {
        $labels = [
            'App\Models\Client' => 'Client',
            'App\Models\Shipment' => 'Shipment',
            'App\Models\Invoice' => 'Invoice',
            'App\Models\Payment' => 'Payment',
            'App\Models\ShipmentBatch' => 'Batch',
            'App\Models\Expense' => 'Expense',
            'App\Models\ExpenseCategory' => 'Expense Category',
            'App\Models\User' => 'User',
            'App\Models\Role' => 'Role',
        ];

        return $labels[$modelType] ?? class_basename($modelType);
    }

    public function getDescriptionAttribute(): string
    {
        $modelLabel = static::getModelLabel($this->model_type);
        $action = ucfirst($this->action);

        if ($this->action === 'created') {
            return "{$action} {$modelLabel}";
        }

        if ($this->action === 'updated') {
            $changes = [];
            if ($this->old_values && $this->new_values) {
                foreach ($this->new_values as $key => $value) {
                    if (isset($this->old_values[$key]) && $this->old_values[$key] !== $value) {
                        $changes[] = $key;
                    }
                }
            }
            $changedFields = implode(', ', $changes) ?: 'fields';
            return "{$action} {$modelLabel} ({$changedFields})";
        }

        if ($this->action === 'deleted') {
            return "{$action} {$modelLabel}";
        }

        return "{$action} {$modelLabel}";
    }
}