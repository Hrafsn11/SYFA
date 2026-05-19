<?php

namespace App\Services\Chatbot;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class ChatApiClient
{
    private string $apiUrl;
    private string $model;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.nvidia.url', 'https://integrate.api.nvidia.com/v1/chat/completions');
        $this->model = config('services.nvidia.model', 'openai/gpt-oss-120b');
        $this->apiKey = config('services.nvidia.api_key', '');
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function send(array $messages): string
    {
        $this->guardCredentials();

        $response = Http::timeout(60)
            ->withHeaders($this->headers())
            ->post($this->apiUrl, $this->buildPayload($messages, false));

        if ($response->failed()) {
            $this->throwForResponse($response);
        }

        return (string) $response->json('choices.0.message.content', '');
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function stream(array $messages, callable $onToken): string
    {
        $this->guardCredentials();

        $payload = json_encode($this->buildPayload($messages, true));

        $fullText = '';
        $buffer = '';

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $this->streamHeaders(),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$fullText, &$buffer, $onToken) {
                $buffer .= $data;
                $lines = explode("\n", $buffer);
                $buffer = array_pop($lines);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!str_starts_with($line, 'data: ')) {
                        continue;
                    }

                    $json = substr($line, 6);
                    if ($json === '[DONE]') {
                        continue;
                    }

                    $chunk = json_decode($json, true);
                    $token = $chunk['choices'][0]['delta']['content'] ?? '';
                    if ($token !== '') {
                        $fullText .= $token;
                        $onToken($token);
                    }
                }

                return strlen($data);
            },
        ]);

        curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Chatbot stream failed: ' . $error);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 400) {
            throw new RuntimeException('Chatbot stream HTTP error', $status);
        }

        return $fullText;
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     * @return array<string, mixed>
     */
    private function buildPayload(array $messages, bool $stream): array
    {
        return [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
            'top_p' => 0.9,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0,
            'stream' => $stream,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function streamHeaders(): array
    {
        return [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
            'Accept: text/event-stream',
        ];
    }

    private function guardCredentials(): void
    {
        if ($this->apiKey === '') {
            Log::warning('Chatbot API key is missing.');
            throw new RuntimeException('Chatbot API key is missing.');
        }
    }

    private function throwForResponse(Response $response): void
    {
        $status = $response->status();
        Log::error('Chatbot API error', [
            'status' => $status,
            'body' => $response->body(),
        ]);

        throw new RuntimeException('Chatbot API request failed.', $status);
    }
}
