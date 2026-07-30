<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use App\Models\Lesson;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private CommentService $service
    ) {}

    public function create(Lesson $lesson,StoreCommentRequest $request )
    {

        $this->authorize('create',  [Comment::class, $lesson]);
        $comment = $this->service->create(
            $lesson,
            auth()->user(),
            $request->validated()
        );
        return new CommentResource($comment);
    }

    public function update( Comment $comment,UpdateCommentRequest $request)
    {
        $this->authorize('update', $comment);
        $comment = $this->service->update(
            $comment,
            $request->validated()
        );
        return new CommentResource($comment);
    }

    public function delete(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $this->service->delete($comment);
        return response()->json([
            'message' => 'Comment deleted successfully.'
        ]);
    }

    public function admindelete(Comment $comment)
    {
        $this->authorize('delete', $comment);
        return response()->json( $this->service->delete($comment));
    }
}
