<?php

namespace App\Domain\Chat;

interface ChatRepositoryInterface
{
    public function save(Message $message): Message;
    public function getMessagesBetween(int $userId1, int $userId2, int $limit = 50): array;

    /**
     * Devuelve el último mensaje de cada conversación donde el usuario es receptor
     * @return Message[]
     */
    public function getLastChatsForUser(int $userId, int $limit = 10): array;

    /**
     * Devuelve un array [other_user_id => count] de mensajes no leídos para el usuario
     */
    public function getUnreadCountForUser(int $userId): array;

    /**
     * Marca como leídos los mensajes recibidos de otro usuario
     */
    public function markAsRead(int $userId, int $otherUserId): void;
}
