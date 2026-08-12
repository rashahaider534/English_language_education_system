<?php

namespace App\Http\Controllers;

use App\Enums\ChatSessionStatus;
use App\Models\ChatSession;
use App\Models\ChatTopic;
use App\Models\UserLevel;
use App\Services\Chat\ChatSessionService;
use App\Services\Chat\GeminiChatService;
use Illuminate\Http\Request;

class ChatSessionController extends Controller
{

    public function __construct(private GeminiChatService $chatService, private ChatSessionService $sessionService) {}

    public function active(Request $request)
    {
        $session = ChatSession::activeForUser($request->user()->id)
            ->with('messages.corrections', 'topic')
            ->first();

        return response()->json(['session' => $session]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mode' => 'required|in:free_talk,topics',
            'topic_id' => 'nullable|exists:chat_topics,id',
        ]);

        $currentLevel = UserLevel::where('user_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->with('level')
            ->latest('enrolled_at')
            ->first();
        $session = $request->user()->chatSessions()->create([
            'mode' => $data['mode'],
            'topic_id' => $data['topic_id'] ?? null,
            'status' => ChatSessionStatus::ACTIVE,
            'level_id_snapshot' => $currentLevel?->level_id ?? null,
            'started_at' => now(),
        ]);

        return response()->json(['session' => $session], 201);
    }

    public function sendMessage(Request $request, ChatSession $session)
    {
        $data = $request->validate(['message' => 'required|string|max:1000']);

        $result = $this->chatService->sendMessage($session, $data['message']);

//        return response()->json([
//            'message' => $reply,
//            'user_message' => $session->messages()
//                ->where('id', '<', $reply->id)
//                ->latest()
//                ->first(['id', 'content', 'corrected_content'])
//                ->load('corrections'),
//        ]);
        return response()->json([
            'message' => $result['assistant_message'],
            'user_message' => $result['user_message'],
        ]);
    }

    public function end(ChatSession $session)
    {
        abort_unless($session->user_id === auth()->id(), 403);

        $summary = $this->sessionService->endSession($session);

        return response()->json(['summary' => $summary]);
    }

    // GET /chat/sessions/history
    public function history(Request $request)
    {
        $sessions = ChatSession::where('user_id', $request->user()->id)
            ->where('status', ChatSessionStatus::ENDED->value)
            ->with('summary', 'topic')
            ->latest('started_at')
            ->paginate(15);

        return response()->json($sessions);
    }

// GET /chat/sessions/{session}/messages (لعرض تفاصيل جلسة قديمة معينة)
    public function showHistorySession(ChatSession $session)
    {
        abort_unless($session->user_id === auth()->id(), 403);

        return response()->json(
            $session->load('messages.corrections', 'summary', 'topic')
        );
    }

    // بـ ChatSessionController أو Service منفصل حسب تفضيلك

    public function availableTopics(Request $request)
    {
        $user = $request->user();

        // كل المستويات يلي للطالب علاقة فعلية فيها (حالي أو مكتمل)
        $userLevelIds = UserLevel::where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'completed'])
            ->pluck('level_id');

        $topics = ChatTopic::where('is_active', true)
            ->whereIn('level_id', $userLevelIds)
            ->select('id', 'title')
            ->orderBy('level_id')
            ->get();

        return response()->json(['topics' => $topics]);
    }
}
