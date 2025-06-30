<?php

namespace App\Application\Chat;

use App\Domain\Chat\ChatRepositoryInterface;

class MarkMessagesAsRead
{
    private ChatRepositoryInterface $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    public function execute(int $userId, int $otherUserId): void
    {
        $this->chatRepository->markAsRead($userId, $otherUserId);
    }
}
