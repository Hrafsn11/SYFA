<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatbotController extends Controller
{
    public function __construct(protected ChatbotService $chatbotService) {}

    /**
     * Kirim pesan ke chatbot dan dapatkan respons (non-streaming fallback).
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user    = Auth::user();
        $message = trim($request->input('message'));

        $history = session('chatbot_history', []);
        $history = array_map(function ($item) {
            $item['content'] = str_replace('…[ringkasan tersimpan]', '', $item['content'] ?? '');
            return $item;
        }, $history);
        $history = array_slice($history, -6);

        $result = $this->chatbotService->chat($user, $message, $history);

        $history[] = ['role' => 'user',  'content' => $message];
        $history[] = ['role' => 'model', 'content' => $result['message']];
        session(['chatbot_history' => $history]);

        return response()->json([
            'success'       => true,
            'message'       => $result['message'],
            'quick_replies' => $result['quick_replies'],
        ]);
    }

    /**
     * Stream respons chatbot via SSE — token muncul real-time di browser.
     */
    public function streamMessage(Request $request): StreamedResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user    = Auth::user();
        $message = trim($request->input('message'));

        $history = session('chatbot_history', []);
        $history = array_map(function ($item) {
            $item['content'] = str_replace('…[ringkasan tersimpan]', '', $item['content'] ?? '');
            return $item;
        }, $history);
        $history = array_slice($history, -6);

        return response()->stream(function () use ($user, $message, $history) {
            // Matikan semua output buffering
            while (ob_get_level()) ob_end_clean();
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', 'off');

            try {
                $fullText = $this->chatbotService->chatStream(
                    $user,
                    $message,
                    $history,
                    function (string $token) {
                        echo 'data: ' . json_encode(['token' => $token]) . "\n\n";
                        flush();
                    }
                );

                // Kirim quick replies setelah stream selesai
                $quickReplies = $this->chatbotService->generateQuickReplies($message, $fullText);
                echo 'data: ' . json_encode(['done' => true, 'quick_replies' => $quickReplies]) . "\n\n";
                flush();

                // Simpan ke session
                $history[] = ['role' => 'user',  'content' => $message];
                $history[] = ['role' => 'model', 'content' => $fullText];
                session(['chatbot_history' => $history]);
                session()->save();

            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SSE stream error: ' . $e->getMessage());
                echo 'data: ' . json_encode(['error' => true]) . "\n\n";
                flush();
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    /**
     * Reset / clear histori percakapan.
     */
    public function clearHistory(): JsonResponse
    {
        session()->forget('chatbot_history');

        return response()->json([
            'success' => true,
            'message' => 'Percakapan telah direset.',
        ]);
    }
}
