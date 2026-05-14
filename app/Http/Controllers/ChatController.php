<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::forUser(auth()->user())
            ->with(['participants', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function getConversations(): JsonResponse
    {
        $conversations = Conversation::forUser(auth()->user())
            ->with(['participants', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) {
                return [
                    'id' => $conversation->id,
                    'uuid' => $conversation->uuid,
                    'type' => $conversation->type,
                    'subject' => $conversation->subject,
                    'other_user' => $conversation->getOtherParticipant(auth()->user()) ? [
                        'id' => $conversation->getOtherParticipant(auth()->user())->id,
                        'name' => $conversation->getOtherParticipant(auth()->user())->name,
                    ] : null,
                    'last_message' => $conversation->latestMessage ? [
                        'body' => $conversation->latestMessage->body,
                        'created_at' => $conversation->latestMessage->created_at->toIso8601String(),
                        'is_mine' => $conversation->latestMessage->user_id === auth()->id(),
                    ] : null,
                    'unread_count' => $conversation->getUnreadCountForUser(auth()->user()),
                    'last_message_at' => $conversation->last_message_at?->toIso8601String(),
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }

    public function show(string $uuid)
    {
        $conversation = Conversation::where('uuid', $uuid)->firstOrFail();

        if (!$conversation->isParticipant(auth()->user())) {
            abort(403);
        }

        $conversation->markAsReadForUser(auth()->user());

        $messages = $conversation->messages()->with('user')->get();

        return view('chat.show', compact('conversation', 'messages'));
    }

    public function getMessages(string $uuid): JsonResponse
    {
        $conversation = Conversation::where('uuid', $uuid)->firstOrFail();

        if (!$conversation->isParticipant(auth()->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->markAsReadForUser(auth()->user());

        $messages = $conversation->messages()->with('user')->get();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'attachment_path' => $message->attachment_path,
                    'attachment_name' => $message->attachment_name,
                    'attachment_type' => $message->attachment_type,
                    'is_image' => $message->isImage(),
                    'is_file' => $message->isFile(),
                    'is_mine' => $message->user_id === auth()->id(),
                    'created_at' => $message->created_at->toIso8601String(),
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                    ],
                ];
            }),
        ]);
    }

    public function startConversation(User $user)
    {
        $conversation = Conversation::getOrCreateDirectConversation(auth()->user(), $user);

        return redirect()->route('chat.show', $conversation);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:2',
            'user_ids.*' => 'exists:users,id',
        ]);

        $conversation = Conversation::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'type' => 'group',
            'subject' => $request->name,
        ]);

        $userIds = $request->user_ids;
        if (!in_array(auth()->id(), $userIds)) {
            $userIds[] = auth()->id();
        }
        $conversation->participants()->attach($userIds);

        return redirect()->route('chat.show', $conversation);
    }

    public function sendMessage(Request $request, string $uuid)
    {
        $conversation = Conversation::where('uuid', $uuid)->firstOrFail();

        if (!$conversation->isParticipant(auth()->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'body' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $message = new Message([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);
        $message->created_at = now();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $message->attachment_path = $file->store('chat-attachments', 'public');
            $message->attachment_name = $file->getClientOriginalName();
            $message->attachment_type = $file->getMimeType();
        }

        $message->save();

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'attachment_path' => $message->attachment_path,
                'attachment_name' => $message->attachment_name,
                'attachment_type' => $message->attachment_type,
                'is_image' => $message->isImage(),
                'is_file' => $message->isFile(),
                'is_mine' => true,
                'created_at' => $message->created_at->toIso8601String(),
                'user' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                ],
            ],
        ]);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        $users = User::where('id', '!=', auth()->id())
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get(['id', 'name', 'email']);

        return response()->json(['users' => $users]);
    }

    public function markAsRead(string $uuid): JsonResponse
    {
        $conversation = Conversation::where('uuid', $uuid)->firstOrFail();

        if (!$conversation->isParticipant(auth()->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->markAsReadForUser(auth()->user());

        return response()->json(['success' => true]);
    }

    public function getUnreadCount(): JsonResponse
    {
        $count = Conversation::forUser(auth()->user())->get()->sum(function ($conversation) {
            return $conversation->getUnreadCountForUser(auth()->user());
        });

        return response()->json(['count' => $count]);
    }
}