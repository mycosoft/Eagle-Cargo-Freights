<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'link',
        'icon',
        'color',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public static function send(
        int $userId,
        string $title,
        string $message,
        ?string $link = null,
        ?string $type = 'info',
        ?string $icon = 'fas fa-bell',
        string $color = 'primary',
        ?int $senderId = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'sender_id' => $senderId ?? auth()->id(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'icon' => $icon,
            'color' => $color,
        ]);
    }

    public static function sendToRole(
        string $role,
        string $title,
        string $message,
        ?string $link = null,
        ?string $type = 'info',
        ?string $icon = 'fas fa-bell',
        string $color = 'primary'
    ): void {
        $users = User::role($role)->get();
        foreach ($users as $user) {
            static::send(
                $user->id,
                $title,
                $message,
                $link,
                $type,
                $icon,
                $color
            );
        }
    }

    public static function sendToMultiple(
        array $userIds,
        string $title,
        string $message,
        ?string $link = null,
        ?string $type = 'info',
        ?string $icon = 'fas fa-bell',
        string $color = 'primary'
    ): void {
        foreach ($userIds as $userId) {
            static::send($userId, $title, $message, $link, $type, $icon, $color);
        }
    }
}