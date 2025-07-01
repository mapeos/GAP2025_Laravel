<?php

namespace App\Application\Chat;

use App\Domain\Chat\ChatRepositoryInterface;
use App\Domain\Chat\Message;

class GetMessagesBetweenUsers
{
    private ChatRepositoryInterface $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    /**
     * @return Message[]
     */
    public function execute(int $userId1, int $userId2, int $limit = 50): array
    {
        return $this->chatRepository->getMessagesBetween($userId1, $userId2, $limit);
    }
}
