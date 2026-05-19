<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ChatbotService
{
    public function __construct(
        private readonly ChatApiClient $apiClient,
        private readonly PromptBuilder $promptBuilder,
        private readonly MessageBuilder $messageBuilder,
        private readonly QuickReplyGenerator $quickReplyGenerator,
        private readonly ChatHistoryStore $historyStore,
    ) {
    }

    public function send(User $user, string $message): ChatResponse
    {
        $history = $this->historyStore->get();
        $systemPrompt = $this->promptBuilder->build($user, $message);
        $messages = $this->messageBuilder->build($systemPrompt, $history, $message);

        try {
            $text = $this->apiClient->send($messages);
        } catch (Throwable $e) {
            Log::error('Chatbot exception: ' . $e->getMessage(), ['code' => $e->getCode()]);

            if ((int) $e->getCode() === 429) {
                return new ChatResponse(
                    '⏳ Asisten sedang sibuk, silakan coba lagi dalam beberapa detik.',
                    ['💰 Cek Status Pinjaman', '🔄 Penyesuaian Cicilan', '📈 Info Investasi']
                );
            }

            return $this->errorResponse();
        }

        $text = $text !== '' ? $text : 'Maaf, saya tidak bisa menjawab saat ini.';
        $quickReplies = $this->quickReplyGenerator->generate($message, $text);

        $this->historyStore->append($message, $text);

        return new ChatResponse($text, $quickReplies);
    }

    public function stream(User $user, string $message): StreamedResponse
    {
        $history = $this->historyStore->get();
        $systemPrompt = $this->promptBuilder->build($user, $message);
        $messages = $this->messageBuilder->build($systemPrompt, $history, $message);

        return response()->stream(function () use ($message, $messages) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', 'off');

            try {
                $fullText = $this->apiClient->stream($messages, function (string $token) {
                    echo 'data: ' . json_encode(['token' => $token]) . "\n\n";
                    flush();
                });

                $quickReplies = $this->quickReplyGenerator->generate($message, $fullText);
                echo 'data: ' . json_encode(['done' => true, 'quick_replies' => $quickReplies]) . "\n\n";
                flush();

                $this->historyStore->append($message, $fullText);
                session()->save();
            } catch (Throwable $e) {
                Log::error('SSE stream error: ' . $e->getMessage(), ['code' => $e->getCode()]);
                echo 'data: ' . json_encode(['error' => true]) . "\n\n";
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    public function clearHistory(): void
    {
        $this->historyStore->clear();
    }

    private function errorResponse(): ChatResponse
    {
        return new ChatResponse(
            'Maaf, terjadi gangguan koneksi. Silakan coba lagi dalam beberapa saat.',
            ['💰 Cek Status Pinjaman', '🔄 Penyesuaian Cicilan', '📈 Info Investasi']
        );
    }
}
