<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'type',
        'subject',
        'last_message_at',
        'created_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at');
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public static function getOrCreateDirectConversation(User $user1, User $user2): self
    {
        $user1Id = min($user1->id, $user2->id);
        $user2Id = max($user1->id, $user2->id);

        $conversation = static::where('type', 'direct')
            ->whereHas('participants', function ($q) use ($user1Id) {
                $q->where('user_id', $user1Id);
            })
            ->whereHas('participants', function ($q) use ($user2Id) {
                $q->where('user_id', $user2Id);
            })
            ->whereHas('participants', function ($q) use ($user1Id, $user2Id) {
                $q->select('conversation_id')
                    ->groupBy('conversation_id')
                    ->havingRaw('COUNT(*) = 2')
                    ->whereIn('user_id', [$user1Id, $user2Id]);
            })
            ->first();

        if (!$conversation) {
            $conversation = static::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'type' => 'direct',
            ]);
            $conversation->participants()->attach([$user1->id, $user2->id]);
        }

        return $conversation;
    }

    public function getOtherParticipant(User $user): ?User
    {
        return $this->participants()->where('user_id', '!=', $user->id)->first();
    }

    public function getUnreadCountForUser(User $user): int
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();
        if (!$participant) return 0;

        $lastRead = $participant->pivot->last_read_at ?? $this->created_at;

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->where('created_at', '>', $lastRead)
            ->count();
    }

    public function markAsReadForUser(User $user): void
    {
        $this->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $this->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }
}