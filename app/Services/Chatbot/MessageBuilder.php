<?php

namespace App\Services\Chatbot;

final class MessageBuilder
{
    /**
     * @param array<int, array{role: string, content: string}> $history
     * @return array<int, array{role: string, content: string}>
     */
    public function build(string $systemPrompt, array $history, string $message): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($history as $item) {
            $role = $item['role'] === 'user' ? 'user' : 'assistant';
            $content = trim((string) $item['content']);
            if ($content === '') {
                continue;
            }

            $messages[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }
}
