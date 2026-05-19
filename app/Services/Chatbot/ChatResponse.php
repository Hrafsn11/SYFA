<?php

namespace App\Services\Chatbot;

final class ChatResponse
{
    /**
     * @param array<int, string> $quickReplies
     */
    public function __construct(
        public readonly string $message,
        public readonly array $quickReplies = []
    ) {
    }

    /**
     * @return array{message: string, quick_replies: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'quick_replies' => $this->quickReplies,
        ];
    }
}
