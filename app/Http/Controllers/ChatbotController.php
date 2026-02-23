<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function __construct(protected ChatbotService $chatbotService) {}

    /**
     * Kirim pesan ke chatbot dan dapatkan respons.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user    = Auth::user();
        $message = trim($request->input('message'));

        $isLocal  = config('services.llm.driver', 'nvidia') === 'local';
        $isGroq   = config('services.llm.driver', 'nvidia') === 'groq';

        // Groq: hemat kuota — 4 pesan + truncate 350 char
        // Kimi/Local: simpan 6 pesan penuh tanpa truncate
        $historyLimit = $isGroq ? 4 : 6;
        $history = session('chatbot_history', []);
        // Bersihkan artefak truncation lama dari session sebelum dipakai
        $history = array_map(function ($item) {
            $item['content'] = str_replace('…[ringkasan tersimpan]', '', $item['content'] ?? '');
            return $item;
        }, $history);
        $history = array_slice($history, -$historyLimit);

        $result = $this->chatbotService->chat($user, $message, $history);

        $history[] = ['role' => 'user',  'content' => $message];
        $botContent = $result['message'];
        if ($isGroq && mb_strlen($botContent) > 350) {
            $botContent = mb_substr($botContent, 0, 350) . '…[ringkasan tersimpan]';
        }
        $history[] = ['role' => 'model', 'content' => $botContent];
        session(['chatbot_history' => $history]);

        return response()->json([
            'success'       => true,
            'message'       => $result['message'],
            'quick_replies' => $result['quick_replies'],
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
