<?php

namespace App\Services\Podcast;

use App\Enums\TopicStatus;
use App\Models\Topic;
use App\Models\User;
use App\Models\Podcast;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class StudentPodcastService
{
    public function getTopics()
    {
        return Topic::where('status', TopicStatus::PUBLISHED)
            ->withCount('podcasts')
            ->paginate(10);
    }

    public function getPodcastsByTopic(User $user, Topic $topic)
    {
        if ($topic->status !== TopicStatus::PUBLISHED) {
            throw ValidationException::withMessages([
                'topic' => 'You cannot view podcasts for this topic.',
            ]);
        }
        $podcasts = $topic->podcasts()->get();
        $openedPodcastIds = $user->podcasts()
            ->pluck('podcasts.id')
            ->toArray();
        $opened = $podcasts->filter(
            fn($podcast) =>
            in_array($podcast->id, $openedPodcastIds)
        );

        $locked = $podcasts->reject(
            fn($podcast) =>
            in_array($podcast->id, $openedPodcastIds)
        );
        $podcastscount = $podcasts->count();
        $openedCount = $opened->count();
        return [
            'total_podcasts' => $podcastscount,
            'opened_count' => $openedCount,
            'opened_podcasts' => $opened,
            'locked_podcasts' => $locked,
        ];
    }

    public function openPodcast(User $user, Podcast $podcast)
    {
        return DB::transaction(function () use ($user, $podcast) {
            $profile = $user->studentProfile()
                ->lockForUpdate()
                ->first();
            if ($profile->points < $podcast->point_required) {
                throw ValidationException::withMessages([
                    'points' => 'You do not have enough points to unlock this podcast.',
                ]);
            }

            if ($user->podcasts()->where('podcast_id', $podcast->id)->exists()) {
                throw ValidationException::withMessages([
                    'podcast' => 'Podcast is already unlocked.',
                ]);
            }

            $user->podcasts()->attach($podcast->id, [
                'listened_at' => now(),
            ]);

            $profile->decrement('points', $podcast->point_required);

            $profile->refresh();

            return [
                'message' => 'Podcast open successfully.',
                'remaining_points' => $profile->points,
            ];
        });
    }

    public function showDetail(User $user, Podcast $podcast)
    {
        $isOpened = $user->podcasts()
            ->where('podcasts.id', $podcast->id)
            ->exists();

        if (! $isOpened) {
            throw ValidationException::withMessages([
                'podcast' => 'You must unlock this podcast first.',
            ]);
        }

        return $podcast->load('topic');
    }
}
