<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\TopicandPodcast\TopicResource;
use App\Services\Podcast\StudentPodcastService;
use App\Http\Resources\TopicandPodcast\PodcastResource;
use App\Models\Topic;
use App\Models\Podcast;
use App\Http\Resources\TopicandPodcast\PodcastDetailsResource;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function __construct(
        private StudentPodcastService $studentpodcastservice
    ) {}

    public function getTopics()
    {
        $topics = $this->studentpodcastservice->getTopics();
        return TopicResource::collection($topics);
    }

    public function getPodcastsByTopic(Topic $topic)
    {
        $podcasts = $this->studentpodcastservice->getPodcastsByTopic(auth()->user(), $topic);
        return
            [
                'total_podcasts' => $podcasts['total_podcasts'],
                'opened_count' => $podcasts['opened_count'],
                'opened_podcasts' => PodcastResource::collection($podcasts['opened_podcasts']),
                'locked_podcasts' => PodcastResource::collection($podcasts['locked_podcasts']),
            ];
    }

    public function openPodcast(Podcast $podcast)
    {
        return response()->json(
            $this->studentpodcastservice->openPodcast(auth()->user(), $podcast)
        );
    }

    public function showDetail(Podcast $podcast)
    {
        $detail = $this->studentpodcastservice->showDetail(auth()->user(), $podcast);
        return new PodcastDetailsResource($detail);
    }
}
