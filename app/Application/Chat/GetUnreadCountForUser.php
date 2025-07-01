<?php

namespace App\Application\Chat;

use App\Domain\Chat\ChatRepositoryInterface;

class GetUnreadCountForUser
{
    private ChatRepositoryInterface $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    public function execute(int $userId): array
    {
        return $this->chatRepository->getUnreadCountForUser($userId);
    }
}
