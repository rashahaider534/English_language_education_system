<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Podcast\StorePodcastRequest;
use App\Http\Requests\Podcast\UpdatePodcastRequest;
use App\Models\Podcast;
use App\Services\Podcast\AdminPodcastService;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function __construct(private AdminPodcastService $adminPodcastService) {}
    public function index(Topic $topic)
    {
        $podcasts = $this->adminPodcastService->index($topic);
        return view('admin.podcasts.index', compact('podcasts', 'topic'));
    }

    public function show(Podcast $podcast)
    {
        $podcast = $this->adminPodcastService->show($podcast);

        return view('admin.podcasts.show', compact('podcast'));
    }

    public function create()
    {
        $topics = $this->adminPodcastService->getTopic();
        return view('admin.podcasts.create', compact('topics'));
    }

    public function store(Topic $topic, StorePodcastRequest $request)
    {
        $this->authorize('create', auth()->user());
        $this->adminPodcastService->create($request->user(), $topic, $request->validated());
        return redirect()
            ->route('admin.podcasts.index', $topic)
            ->with('success', 'Podcast created successfully');
    }

    public function edit(Podcast $podcast)
    {
        return view('admin.podcasts.edit', compact('podcast'));
    }

    public function update(Podcast $podcast, UpdatePodcastRequest $request)
    {
        $this->authorize('update', $podcast);
        $podcast = $this->adminPodcastService->update($podcast, $request->validated());
        return redirect()
            ->route('admin.podcasts.show', $podcast)
            ->with('success', 'Podcast updated successfully');
    }

    public function destroy(Podcast $podcast)
    {
        $this->authorize('delete', $podcast);
        $this->adminPodcastService->delete($podcast);
        return redirect()
            ->route('admin.podcasts.index', $podcast->topic)
            ->with('success', 'Podcast deleted successfully');
    }
}
