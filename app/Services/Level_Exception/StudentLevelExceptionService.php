<?php

namespace App\Services\Level_Exception;

use App\Enums\LevelExceptionStatus;
use App\Jobs\SendNotificationJob;
use App\Services\LevelAccessService;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Level;
use App\Models\User;
use App\Models\LevelException;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;


class StudentLevelExceptionService
{
    public function __construct(
         private LevelAccessService $levelAccessService,
         private NotificationService $notificationService
    ) {}
    public function index(User $user, ?string $status = null)
    {
        return LevelException::query()
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->where('user_id', $user->id)
            ->with([
                'requestedLevel:id,name_ar,name_en',

            ])
            ->paginate(10);
    }
    public function  view(LevelException $levelException)
    {
        return  $levelException->load('media');
    }

    public function create(Level $level, User $user, array $data)
    {    //ارسال اشعار للادمن بطلب استثناء جديد
        $allowedOrder = $this->levelAccessService->getAllowedOrder($user);
        $context = $this->levelAccessService->getUserLevelContext($user);
        $lockedLevels = $this->levelAccessService->getLockedLevels(
            $allowedOrder,
            $context['userLevelIds'],
            $context['approvedExceptionLevelIds']
        );

        if (! $lockedLevels->contains('id', $level->id)) {
            throw ValidationException::withMessages([
                'level' => __('messages.only_request_locked_levels'),
            ]);
        }
        $hasOpenRequest = LevelException::query()
            ->where('user_id', $user->id)
            ->where('requested_level_id', $level->id)
            ->whereIn('status', [
                LevelExceptionStatus::PENDING,
                LevelExceptionStatus::IN_REVIEW,
                LevelExceptionStatus::APPROVED,
            ])
            ->exists();
        if ($hasOpenRequest) {
            throw ValidationException::withMessages([
                'level' => __('messages.already_have_level_exception_request'),
            ]);
        }
        return DB::transaction(function () use ($level, $user, $data) {
            $recommendedLevel = $this->levelAccessService->getRecommendedLevel($user);
            if (!$recommendedLevel) {
                throw ValidationException::withMessages([
                    'placement' => __('messages.must_complete_placement_test'),
                ]);
            }
            $levelException = LevelException::create([
                'user_id' => $user->id,
                'requested_level_id' => $level->id,
                'recommended_level_id' => $recommendedLevel->id,
                'status' => LevelExceptionStatus::PENDING,
                'reason' => $data['reason']
            ]);
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $levelException
                        ->addMedia($file)
                        ->toMediaCollection('attachments');
                }
            }

            $admin= User::role('super-admin')->pluck('id')->toArray();
            SendNotificationJob::dispatch(
                $admin,
                'New Level Exception Request',
                'A student has submitted a request to access a locked level.',
                [ 'level_exception_id' =>$levelException->id],
                'level-exception'
            );

            return $levelException;

        });
    }
    public function update(LevelException $levelException, array $data)
    {
        if (! in_array($levelException->status, [
            LevelExceptionStatus::PENDING,
        ])) {
            throw ValidationException::withMessages([
                'level' => __('messages.cannot_update_request'),
            ]);
        }

        return DB::transaction(function () use ($levelException, $data) {

            $levelException->update([
                'reason' => $data['reason'],
            ]);

            if (!empty($data['attachments'])) {

                foreach ($data['attachments'] as $file) {
                    $levelException
                        ->addMedia($file)
                        ->toMediaCollection('attachments');
                }
            }

            return $levelException->refresh();
        });
    }

    public function deleteAttachment(LevelException $levelException, Media $media)
    {
        if ($levelException->status !== LevelExceptionStatus::PENDING) {
            throw ValidationException::withMessages([
                'level' => __('messages.cannot_update_request'),
            ]);
        }

        if (
            $media->model_id !== $levelException->id ||
            $media->model_type !==Relation::getMorphAlias(LevelException::class)
        ) {
            abort(404);
        }

        $media->delete();
    }

    public function delete(LevelException $levelException)
    {
        if (! in_array($levelException->status, [
            LevelExceptionStatus::PENDING,
        ])) {
            throw ValidationException::withMessages([
                'level' => __('messages.cannot_delete_request'),
            ]);
        }
        $levelException->delete();
        return [
        'message' => __('messages.request_deleted_successfully')
        ];
    }
}
