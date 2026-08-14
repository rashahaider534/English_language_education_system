<?php

namespace App\Services\Podcast;

use App\Enums\TopicStatus;
use App\Jobs\SendNotificationJob;
use App\Models\Podcast;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AdminPodcastService
{
    public function index(Topic $topic)
    {
        return $topic->podcasts()
            ->paginate(10);
    }

    public function show(Podcast $podcast)
    {
        return $podcast->load([
            'topic',
            'creator',
        ]);
    }

    public function create(User $user, Topic $topic, array $data)
    { //اشعار للطلاب لما يكون التوبك منشور ويضيف بودكاست جديد
        return DB::transaction(function () use ($user, $topic, $data) {
            $podcast = Podcast::create(
                [
                    'topic_id' => $topic->id,
                    'point_required' => $data['point_required'],
                    'name_en' => $data['name_en'],
                    'name_ar' => $data['name_ar'],
                    'created_by' => $user->id,
                ]
            );
            if (isset($data['video'])) {
                $podcast
                    ->addMedia($data['video'])
                    ->toMediaCollection('podcast_video');
            }
            $podcast->refresh();

            if ($topic->status === TopicStatus::PUBLISHED) {

                $students = User::role('student', 'api')
                    ->pluck('id')
                    ->toArray();

                SendNotificationJob::dispatch(
                    $students,
                    'New Podcast Available',
                    "A new podcast has been added to the topic: {$topic->name_en}.",
                    [
                        'podcast_id' => $podcast,
                        'topic_id' => $topic,
                    ],
                    'podcast-created'
                );
            }
            return $podcast->load(['topic', 'creator', 'media']);
        });
    }

    public function getTopic()
    {
        return Topic::all();
    }

    public function update(Podcast $podcast, array $data)
    {
        return DB::transaction(function () use ($podcast, $data) {
            if ($podcast->topic->status === TopicStatus::PUBLISHED) {
                $allowedFields = [
                    'name_ar',
                    'name_en',
                    'point_required',
                ];
                $data = array_intersect_key(
                    $data,
                    array_flip($allowedFields)
                );
            }
            if (isset($data['video'])) {
                $podcast
                    ->addMedia($data['video'])
                    ->toMediaCollection('podcast_video');
            }
            $podcast->update($data);
            $podcast->refresh();
            return $podcast->load(['topic', 'creator', 'media']);
        });
    }

    public function delete(Podcast $podcast)
    {
        if ($podcast->topic->status === TopicStatus::PUBLISHED) {
            throw ValidationException::withMessages([
                'podcast' => 'You are not allowed to delete this podcast.',
            ]);
        } else {
            $podcast->delete();
            return ['podcast deleted successfully'];
        }
    }
}
