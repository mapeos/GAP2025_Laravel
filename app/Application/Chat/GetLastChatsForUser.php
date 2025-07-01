<?php

namespace App\Application\Chat;

use App\Domain\Chat\ChatRepositoryInterface;
use App\Domain\Chat\Message;

class GetLastChatsForUser
{
    private ChatRepositoryInterface $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    /**
     * Devuelve el último mensaje de cada conversación donde el usuario es receptor
     * @return Message[]
     */
    public function execute(int $userId, int $limit = 10): array
    {
        return $this->chatRepository->getLastChatsForUser($userId, $limit);
    }
}
