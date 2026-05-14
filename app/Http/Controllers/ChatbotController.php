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
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user    = Auth::user();
        $message = trim($data['message']);

        $result = $this->chatbotService->send($user, $message);

        return response()->json([
            'success'       => true,
            'message'       => $result->message,
            'quick_replies' => $result->quickReplies,
        ]);
    }

    /**
     * Stream respons chatbot via SSE — token muncul real-time di browser.
     */
    public function streamMessage(Request $request): StreamedResponse
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user    = Auth::user();
        $message = trim($data['message']);

        return $this->chatbotService->stream($user, $message);
    }

    /**
     * Reset / clear histori percakapan.
     */
    public function clearHistory(): JsonResponse
    {
        $this->chatbotService->clearHistory();

        return response()->json([
            'success' => true,
            'message' => 'Percakapan telah direset.',
        ]);
    }
}
