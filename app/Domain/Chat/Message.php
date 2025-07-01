<?php

namespace App\Domain\Chat;

class Message
{
    public int $senderId;
    public int $receiverId;
    public string $content;
    public ?string $createdAt;
    public ?int $id;

    public function __construct(int $senderId, int $receiverId, string $content, ?string $createdAt = null, ?int $id = null)
    {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }
}
