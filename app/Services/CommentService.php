<?php

namespace App\Services;
use App\Models\Lesson;
class CommentService
{
    public function getComments(Lesson $lesson)
    {
        return $lesson->comments()
            ->with('user')
            ->latest()
            ->paginate(10);
    }
}
