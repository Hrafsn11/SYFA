<?php

namespace App\Services\Chatbot;

final class ChatHistoryStore
{
    private const SESSION_KEY = 'chatbot_history';
    private const TRUNCATION_MARKER = '…[ringkasan tersimpan]';

    /**
     * @return array<int, array{role: string, content: string}>
     */
    public function get(int $limit = 6): array
    {
        $history = session(self::SESSION_KEY, []);

        $history = array_values(array_filter($history, function (array $item): bool {
            return !empty($item['role']) && !empty($item['content']);
        }));

        $history = array_map(function (array $item): array {
            $content = $this->sanitize((string) $item['content']);
            if ($content === '') {
                return [];
            }

            return [
                'role' => (string) $item['role'],
                'content' => $content,
            ];
        }, $history);

        $history = array_values(array_filter($history));

        return array_slice($history, -$limit);
    }

    public function append(string $userMessage, string $assistantMessage): void
    {
        $history = session(self::SESSION_KEY, []);

        $history[] = [
            'role' => 'user',
            'content' => $this->sanitize($userMessage),
        ];
        $history[] = [
            'role' => 'model',
            'content' => $this->sanitize($assistantMessage),
        ];

        session([self::SESSION_KEY => $history]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    private function sanitize(string $content): string
    {
        return rtrim(str_replace(self::TRUNCATION_MARKER, '', $content));
    }
}
