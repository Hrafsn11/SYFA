<?php

namespace App\Services;

use App\Models\User;
use App\Services\Chatbot\ChatResponse;
use App\Services\Chatbot\ChatbotService as CoreChatbotService;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ChatbotService
{
	public function __construct(private readonly CoreChatbotService $core)
	{
	}

	public function send(User $user, string $message): ChatResponse
	{
		return $this->core->send($user, $message);
	}

	public function stream(User $user, string $message): StreamedResponse
	{
		return $this->core->stream($user, $message);
	}

	public function clearHistory(): void
	{
		$this->core->clearHistory();
	}
}
