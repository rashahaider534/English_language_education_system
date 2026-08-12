<?php

namespace App\Services\Chat;

use App\Enums\ChatErrorType;
use App\Enums\ChatSessionStatus;
use App\Models\ChatSession;
use App\Models\ChatSessionSummary;
use Illuminate\Support\Facades\DB;

class ChatSessionService
{
    private const MIN_MESSAGES_FOR_XP = 3;   // حد أدنى حتى تستأهل الجلسة XP أصلاً
    private const XP_PER_MESSAGE = 2;        // نقطتين لكل رسالة طالب
    private const MAX_XP_PER_SESSION = 30;   // سقف أقصى لمنع الاستغلال

    public function endSession(ChatSession $session): ChatSessionSummary
    {
        if (! $session->isActive()) {
            // الجلسة أصلاً منتهية، رجعي الملخص الموجود بدل ما تعملي واحد جديد
            return $session->summary;
        }

        $session->update([
            'status' => ChatSessionStatus::ENDED,
            'ended_at' => now(),
        ]);

        $stats = $this->calculateCorrectionStats($session);
        $xpAwarded = $this->calculateXp($session);

        $summary = $session->summary()->create([
            'overall_feedback' => $this->buildFeedbackText($stats),
            'strengths' => $stats['strengths'],
            'weaknesses' => $stats['weaknesses'],
            'estimated_level' => null, // ممكن تحسبيها لاحقاً بمنطق أعقد أو عبر Gemini
            'xp_awarded' => $xpAwarded,
        ]);

        if ($xpAwarded > 0) {
            $this->awardXp($session, $xpAwarded);
        }

        return $summary;
    }

    /**
     * بتحسب توزيع الأخطاء حسب النوع، بدون أي استدعاء لـ Gemini —
     * كلها حسابات محلية بسيطة على البيانات المخزنة أصلاً بـ chat_corrections
     */
    private function calculateCorrectionStats(ChatSession $session): array
    {
        $errorCounts = DB::table('chat_corrections')
            ->join('chat_messages', 'chat_corrections.chat_message_id', '=', 'chat_messages.id')
            ->where('chat_messages.chat_session_id', $session->id)
            ->select('chat_corrections.error_type', DB::raw('count(*) as total'))
            ->groupBy('chat_corrections.error_type')
            ->orderByDesc('total')
            ->get();

        $weaknesses = $errorCounts->take(3)->map(fn ($row) => [
            'error_type' => $row->error_type,
            'count' => $row->total,
        ])->values()->toArray();

        $totalMessages = $session->messages()->where('role', 'user')->count();
        $totalErrors = $errorCounts->sum('total');

        $strengths = [];
        if ($totalMessages > 0 && $totalErrors / max($totalMessages, 1) < 0.3) {
            $strengths[] = 'low_error_rate';
        }
        if ($totalMessages >= 5) {
            $strengths[] = 'active_participation';
        }

        return [
            'weaknesses' => $weaknesses,
            'strengths' => $strengths,
            'total_errors' => $totalErrors,
            'total_messages' => $totalMessages,
        ];
    }

    private function buildFeedbackText(array $stats): string
    {
        if ($stats['total_messages'] === 0) {
            return 'لم تكتمل هذه الجلسة بما يكفي لتقديم ملاحظات.';
        }

        if ($stats['total_errors'] === 0) {
            return 'أداء ممتاز! لم تُسجل أي أخطاء في هذه الجلسة.';
        }

        $topError = $stats['weaknesses'][0]['error_type'] ?? null;
        $errorLabel = $topError instanceof ChatErrorType ? $topError->value : $topError;

        return "أحسنت على المشاركة! لاحظنا بعض الأخطاء المتكررة في: {$errorLabel}. راجع التصحيحات في هذه الجلسة للتعلم منها.";
    }

    /**
     * XP متدرج حسب عدد رسايل الطالب الفعلية، بسقف أقصى يمنع الاستغلال
     * (فتح رسايل قصيرة كتير للحصول على XP مبالغ فيه)
     */
    private function calculateXp(ChatSession $session): int
    {
        $userMessageCount = $session->messages()->where('role', 'user')->count();

        if ($userMessageCount < self::MIN_MESSAGES_FOR_XP) {
            return 0;
        }

        return min($userMessageCount * self::XP_PER_MESSAGE, self::MAX_XP_PER_SESSION);
    }

    private function awardXp(ChatSession $session, int $xp): void
    {
        $session->user->studentProfile()->increment('points', $xp);

    }

}
