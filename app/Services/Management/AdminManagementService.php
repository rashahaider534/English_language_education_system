<?php

namespace App\Services\Management;

use App\Models\User;
use App\Models\Course;
use App\Models\Level;
use App\Models\Topic;
use App\Models\ContentReview;
use App\Models\LevelException;
use App\Models\PlacementTest;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminManagementService
{

    public function getAdmins(?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return User::withTrashed()->role('admin', 'web')->with('permissions')->when($search, function ($query) use ($search) {
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

    public function getAdminCounts(): array
    {
        return [
            'totalCount' => User::withTrashed()->role('admin', 'web')->count(),
            'activeCount' => User::role('admin', 'web')->whereNull('deleted_at')->count(),
            'inactiveCount' => User::onlyTrashed()->role('admin', 'web')->count(),
        ];
    }

    public function getPermissions()
    {
        return Permission::where('guard_name', 'web')->orderBy('name')->get();
    }

    public function getAdminActivity(User $admin): array
    {
        return [
            'reviews' => ContentReview::where('reviewer_id', $admin->id)->with('reviewable')->latest('claimed_at')->get(),
            'levels' => Level::where('created_by', $admin->id)->latest()->get(),
            'courses' => Course::where('created_by', $admin->id)->with('level')->latest()->get(),
            'topics' => Topic::where('created_by', $admin->id)->latest()->get(),
            'levelExceptions' => LevelException::where('executed_by', $admin->id)->with(['requestedLevel', 'recommendedLevel'])->latest('executed_at')->get(),
            'placementTests' => PlacementTest::where('created_by', $admin->id)->with('tests')->latest()->get(),
        ];
    }
}
