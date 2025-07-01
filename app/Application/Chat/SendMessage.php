<?php

namespace App\Application\Chat;

use App\Domain\Chat\ChatRepositoryInterface;
use App\Domain\Chat\Message;

class SendMessage
{
    private ChatRepositoryInterface $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    public function execute(int $senderId, int $receiverId, string $content): Message
    {
        $message = new Message($senderId, $receiverId, $content);
        return $this->chatRepository->save($message);
    }
}
