<?php

namespace Database\Seeders;

use App\Models\ChatTopic;
use Illuminate\Database\Seeder;

class ChatTopicSeeder extends Seeder
{
    public function run(): void
    {
        $levelId = 1; // عدّلي حسب الـ id الفعلي لمستوى Level 1 عندك

        $topics = [
            [
                'title' => 'At the Airport',
                'description' => 'Practice checking in, going through security, and boarding a flight.',
                'focus_points' => 'checking in, security questions, boarding pass, luggage',
                'system_prompt_addon' => 'Guide the student through an airport scenario. Cover: check-in, security, and boarding. You may act as airport staff if the student wants to roleplay.',
            ],
            [
                'title' => 'Ordering at a Restaurant',
                'description' => 'Practice ordering food, asking about the menu, and paying the bill.',
                'focus_points' => 'ordering food, asking about ingredients, paying the bill',
                'system_prompt_addon' => 'Guide the student through a restaurant scenario. Cover: greeting, taking an order, answering menu questions, and the bill.',
            ],
            [
                'title' => 'Meeting Someone New',
                'description' => 'Practice introducing yourself and making small talk.',
                'focus_points' => 'greetings, introducing yourself, asking simple questions about someone',
                'system_prompt_addon' => 'Guide the student through a casual introduction scenario. Keep it friendly and simple, appropriate for a beginner level.',
            ],
            [
                'title' => 'Shopping for Clothes',
                'description' => 'Practice asking for sizes, colors, and prices while shopping.',
                'focus_points' => 'sizes, colors, prices, trying on clothes',
                'system_prompt_addon' => 'Guide the student through a clothing store scenario. You may act as a shop assistant if the student wants to roleplay.',
            ],
            [
                'title' => 'Talking About Your Daily Routine',
                'description' => 'Practice describing your typical day using present simple tense.',
                'focus_points' => 'daily activities, time expressions, present simple tense',
                'system_prompt_addon' => 'Ask the student about their daily routine. Encourage use of present simple tense (e.g., "I wake up at...", "I go to...").',
            ],
        ];

        foreach ($topics as $topic) {
            ChatTopic::create([
                ...$topic,
                'level_id' => $levelId,
                'is_active' => true,
            ]);
        }
    }
}
