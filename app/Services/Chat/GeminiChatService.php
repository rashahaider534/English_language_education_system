<?php

namespace App\Services\Chat;

use App\Enums\ChatMessageRole;
use App\Models\ChatMessage;
use App\Models\ChatSession;

class GeminiChatService
{
    public function __construct(
        private ChatPromptBuilder $promptBuilder,
        private GeminiClient $geminiClient,
    ) {}

    public function sendMessage(ChatSession $session, string $userMessage): array
    {
        $systemPrompt = $this->promptBuilder->build($session);

        $history = $session->messages()
            ->latest()
            ->take(15)
            ->get()
            ->reverse()
            ->values();

        $result = $this->geminiClient->send($systemPrompt, $history, $userMessage);

        $validCorrections = collect($result['corrections'])->filter(function ($correction) use ($userMessage) {
            return $this->fragmentExistsInMessage($correction['original'] ?? '', $userMessage);
        });
        // خزني رسالة الطالب + النسخة المصححة الكاملة (لو حبيتي تبنيها من الـ corrections)
        $userMsg = $session->messages()->create([
            'role' => ChatMessageRole::USER,
            'content' => $userMessage,
            'corrected_content' => $this->buildCorrectedContent($userMessage, $validCorrections),
        ]);

//        foreach ($result['corrections'] as $correction) {
//            $userMsg->corrections()->create([
//                'error_type' => $correction['error_type'],
//                'original_fragment' => $correction['original'],
//                'corrected_fragment' => $correction['corrected'],
//                'explanation' => $correction['explanation_ar'],
//            ]);
//        }


        foreach ($validCorrections as $correction) {
            $userMsg->corrections()->create([
                'error_type' => $correction['error_type'],
                'original_fragment' => $correction['original'],
                'corrected_fragment' => $correction['corrected'],
                'explanation' => $correction['explanation_ar'],
            ]);
        }

        // خزني رد الـ AI
        $assistantMsg = $session->messages()->create([
            'role' => ChatMessageRole::ASSISTANT,
            'content' => $result['reply'],
            'metadata' => ['topic_relevance' => $result['topic_relevance']],
        ]);

        return [
            'user_message' => $userMsg->load('corrections'),
            'assistant_message' => $assistantMsg,
        ];
    }
    private function fragmentExistsInMessage(string $fragment, string $message): bool
    {
        if (empty($fragment)) {
            return false;
        }

        return str_contains(
            strtolower($message),
            strtolower(trim($fragment))
        );
    }

    private function buildCorrectedContent(string $original, $corrections): ?string
    {
        if ($corrections->isEmpty()) {
            return null;
        }

        $corrected = $original;
        foreach ($corrections as $c) {
            $corrected = str_replace($c['original'], $c['corrected'], $corrected);
        }

        return $corrected;
    }
}
