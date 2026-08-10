<?php

namespace App\Services\Management;

use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeacherManagementService
{

    public function getTeachers(?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return User::withTrashed()->role('teacher')->with('teacherProfile')->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        })->when($status !== null && $status !== '', function ($query) use ($status) {
            if ($status === 'active') {
                $query->whereNull('deleted_at');
            }
            if ($status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        })->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
    }

    public function getTeacherCounts(): array
    {
        $totalCount = User::withTrashed()->role('teacher', 'api')->count();
        $activeCount = User::role('teacher', 'api')->whereNull('deleted_at')->count();
        $inactiveCount = User::onlyTrashed()->role('teacher', 'api')->count();
        return ['totalCount' => $totalCount, 'activeCount' => $activeCount, 'inactiveCount' => $inactiveCount,];
    }

    public function courses(User $teacher)
    {
        abort_unless($teacher->hasRole('teacher'), 404);

        $courses = Course::where('teacher_id',$teacher->id)->latest()->paginate(15);
        $teacher->load('teacherProfile');

        return ['courses'=>$courses,'teacher'=>$teacher];
    }
}
