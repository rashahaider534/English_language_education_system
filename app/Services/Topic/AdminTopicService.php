<?php

namespace App\Services\Topic;

use App\Models\Topic;
use App\Models\User;
use App\Enums\TopicStatus;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminTopicService
{
    public function index()
    {
        $topics = Topic::with(['creator', 'media'])
            ->withCount('podcasts')
            ->get();
        return $topics;
    }

    public function create(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $topic = Topic::create(
                [
                    'name_en' => $data['name_en'],
                    'name_ar' => $data['name_ar'],
                    'created_by' => $user->id,
                    'status' => TopicStatus::PENDING->value
                ]
            );
            if (isset($data['image'])) {
                $topic
                    ->addMedia($data['image'])
                    ->toMediaCollection('topic_image');
            }
            $topic->refresh();
            return $topic->load(['creator', 'media']);
        });
    }

    public function update(Topic $topic, array $data)
    {
        return DB::transaction(function () use ($topic, $data) {
            if (isset($data['image'])) {
                $topic
                    ->addMedia($data['image'])
                    ->toMediaCollection('topic_image');
            }
            $topic->update($data);
            $topic->refresh();
            return $topic->load(['creator', 'media']);
        });
    }

    public function delete(Topic $topic)
    {
        if ($topic->status === TopicStatus::PUBLISHED) {
            throw ValidationException::withMessages([
                'topic' => 'You are not allowed to delete this topic.',
            ]);
        } else {
            $topic->delete();
            return ['topic deleted successfully'];
        }
    }

    public function publish(Topic $topic)
    {
        if ($topic->status === TopicStatus::PUBLISHED) {
            throw ValidationException::withMessages([
                'topic' => 'This topic is already published.',
            ]);
        } else {
            $topic->update(['status' => TopicStatus::PUBLISHED]);
            $students = User::role('student', 'api')
                ->pluck('id')
                ->toArray();
            SendNotificationJob::dispatch(
                $students,
                'New Topic Published',
                "A new topic has been published: {$topic->name_en}.",
                [
                    'topic_id' => $topic->id,
                ],
                'topic-published'
            );
            
            return ['topic published successfully'];
        }
    }

    //في تابع النشر لا تنسيه
}
