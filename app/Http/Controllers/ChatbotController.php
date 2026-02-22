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

        // Ambil history dari session (max 6 pesan terakhir agar hemat token)
        $history = session('chatbot_history', []);
        $history = array_slice($history, -6);

        $result = $this->chatbotService->chat($user, $message, $history);

        // Simpan percakapan ke session
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
