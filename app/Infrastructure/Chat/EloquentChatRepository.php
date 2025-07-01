<?php

namespace App\Infrastructure\Chat;

use App\Domain\Chat\ChatRepositoryInterface;
use App\Domain\Chat\Message;
use App\Models\ChatMessage as EloquentMessage;

class EloquentChatRepository implements ChatRepositoryInterface
{
    public function save(Message $message): Message
    {
        $eloquent = new EloquentMessage();
        $eloquent->sender_id = $message->senderId;
        $eloquent->receiver_id = $message->receiverId;
        $eloquent->content = $message->content;
        $eloquent->save();
        return new Message(
            $eloquent->sender_id,
            $eloquent->receiver_id,
            $eloquent->content,
            $eloquent->created_at,
            $eloquent->id
        );
    }

    public function getMessagesBetween(int $userId1, int $userId2, int $limit = 50): array
    {
        $messages = EloquentMessage::where(function($q) use ($userId1, $userId2) {
                $q->where('sender_id', $userId1)->where('receiver_id', $userId2);
            })->orWhere(function($q) use ($userId1, $userId2) {
                $q->where('sender_id', $userId2)->where('receiver_id', $userId1);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        return $messages->map(function($m) {
            return new Message($m->sender_id, $m->receiver_id, $m->content, $m->created_at, $m->id);
        })->all();
    }

    public function getLastChatsForUser(int $userId, int $limit = 10): array
    {
        // Obtener el último mensaje de cada conversación donde el usuario es receptor o emisor
        $sub = \App\Models\ChatMessage::selectRaw('LEAST(sender_id, receiver_id) as u1, GREATEST(sender_id, receiver_id) as u2, MAX(id) as max_id')
            ->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->groupBy('u1', 'u2');

        $ids = $sub->pluck('max_id');
        $messages = \App\Models\ChatMessage::whereIn('id', $ids)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        return $messages->map(function($m) {
            return new Message($m->sender_id, $m->receiver_id, $m->content, $m->created_at, $m->id);
        })->all();
    }

    public function getUnreadCountForUser(int $userId): array
    {
        // Devuelve un array [other_user_id => count]
        $messages = \App\Models\ChatMessage::where('receiver_id', $userId)
            ->whereNull('read_at')
            ->selectRaw('sender_id, COUNT(*) as count')
            ->groupBy('sender_id')
            ->get();
        $result = [];
        foreach ($messages as $msg) {
            $result[$msg->sender_id] = $msg->count;
        }
        return $result;
    }

    public function markAsRead(int $userId, int $otherUserId): void
    {
        \App\Models\ChatMessage::where('receiver_id', $userId)
            ->where('sender_id', $otherUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
