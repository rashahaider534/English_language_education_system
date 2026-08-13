<?php

namespace App\Services\Chat;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class GeminiClient
{
    private string $apiKey;
   private string $model = 'gemini-3.6-flash';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * @return array{reply: string, corrections: array, topic_relevance: string}
     */
    public function send(string $systemInstruction, Collection $history, string $newMessage): array
    {
        $contents = $history->map(fn (ChatMessage $msg) => [
            'role' => $msg->isFromUser() ? 'user' : 'model',
            'parts' => [['text' => $msg->content]],
        ])->push([
            'role' => 'user',
            'parts' => [['text' => $newMessage]],
        ])->values()->toArray();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                    'temperature' => 0.7,
                ],
            ]
        );

        if ($response->failed()) {
            throw new \RuntimeException('Gemini API request failed: ' . $response->body());
        }

        $rawText = $response->json('candidates.0.content.parts.0.text');

        return $this->parseResponse($rawText);
    }

    private function parseResponse(?string $rawText): array
    {
        if (! $rawText) {
            return $this->fallbackResponse();
        }

        $decoded = json_decode($rawText, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! isset($decoded['reply'])) {
            return $this->fallbackResponse();
        }

        return [
            'reply' => $decoded['reply'],
            'corrections' => $decoded['corrections'] ?? [],
            'topic_relevance' => $decoded['topic_relevance'] ?? 'on_topic',
        ];
    }

    private function fallbackResponse(): array
    {
        return [
            'reply' => "Sorry, could you say that again in a different way?",
            'corrections' => [],
            'topic_relevance' => 'on_topic',
        ];
    }
}
