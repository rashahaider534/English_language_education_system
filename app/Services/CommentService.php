<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\Lesson;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommentService
{
    public function getComments(Lesson $lesson)
    {
        return $lesson->comments()
            ->with('user')
            ->latest()
            ->paginate(10);
    }

    public function create(Lesson $lesson, User $user, array $data)
    {
            if ($lesson->status !== ContentStatus::PUBLISHED->value) {
                throw ValidationException::withMessages([
                    'comment' => 'You cannot be create comment for this lesson.',
                ]);
            }
            return Comment::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'comment' => $data['comment'],
                'created_at' => now(),
            ]);

    }
    public function update(Comment $comment,  array $data)
    {
        if (in_array($comment->lesson->status, ['archived', 'closed'])) {
            throw ValidationException::withMessages([
                'comment' => 'Comments cannot be updated for this lesson.',
            ]);
        }
        $comment->update($data);
        return $comment;
    }
    public function delete(Comment $comment)
    {
        $comment->delete();
        return ['comment deleted successfully'];
    }
}
