<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Topic\StoreTopicRequest;
use App\Http\Requests\Topic\UpdateTopicRequest;
use App\Services\Topic\AdminTopicService;
use App\Models\Topic;


class TopicController extends Controller
{
    public function __construct(private AdminTopicService $adminTopicService) {}

    public function index()
    {
        $topics = $this->adminTopicService->index();
        return view('admin.topics.index', compact('topics'));
    }

    public function create()
    {
        return view('admin.topics.create');
    }

    public function store(StoreTopicRequest $request)
    {
        // $this->authorize('create', auth()->user());
        $this->adminTopicService->create($request->user(), $request->validated());
        return redirect()
            ->route('topics.index')
            ->with('success', 'Topic created successfully');
    }

    public function edit(Topic $topic)
    {
        return view('admin.topics.edit', compact('topic'));
    }

    public function update(Topic $topic, UpdateTopicRequest $request)
    {
        // $this->authorize('update', $topic);
        $updatedTopic = $this->adminTopicService->update($topic, $request->validated());
        return redirect()
            ->route('topics.index')
            ->with('success', 'Topic updated successfully');
    }

    public  function destroy(Topic $topic)
    {
        // $this->authorize('delete', $topic);
        $this->adminTopicService->delete($topic);
        return redirect()
            ->route('topics.index')
            ->with('success', 'Topic deleted successfully');
    }

    public function publish(Topic $topic)
    {
        // $this->authorize('publish', $topic);
        $this->adminTopicService->publish($topic);
        return redirect()
            ->route('topics.index')
            ->with('success', 'Topic published successfully');
    }
}
