<?php

namespace App\Services\Level_Exception;

use App\Enums\LevelExceptionStatus;
use App\Jobs\SendNotificationJob;
use App\Models\LevelException;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AdminLevelExceptionService
{
    public function index(?string $status = null)
    {
        return LevelException::query()
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(10);
    }

    public function show(LevelException $levelException)
    {
        return $levelException->load([
            'media',
            'requestedLevel:id,name_en,name_ar',
            'user:id,first_name,last_name,email',
            'recommendedLevel:id,name_en,name_ar'
        ]);
    }

    public function startReview(User $user, LevelException $levelException)
    {
        return DB::transaction(function () use ($user, $levelException) {

            $levelException = LevelException::where('id', $levelException->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($levelException->status !== LevelExceptionStatus::PENDING) {
                throw ValidationException::withMessages([
                    'status' => 'Request is already under review.',
                ]);
            }

            $levelException->update([
                'status' => LevelExceptionStatus::IN_REVIEW,
                'executed_by' => $user->id,
            ]);

            return $levelException;
        });
    }

    public function approve(User $user, LevelException $levelException, array $data)
    { //ارسال اشعار للطالب بالموافقة
        if ($levelException->status !== LevelExceptionStatus::IN_REVIEW) {
            throw ValidationException::withMessages([
                'status' => 'You cannot approve ',
            ]);
        }
        $levelException->update([
            'status' => LevelExceptionStatus::APPROVED,
            'executed_by' => $user->id,
            'executed_at' => now(),
            'review_note' => $data['review_note']
        ]);

        $student = User::find($levelException->user_id);
        if ($student) {
            SendNotificationJob::dispatch(
                [$student->id],
                'Level Exception Approved',
                'Your request to access the level has been approved.',
                [
                    'level_exception_id' => $levelException->id,
                ],
                'level-exception-approved'
            );
        }

        return $levelException;
    }

    public function reject(User $user, LevelException $levelException, array $data)
    { //ارسال اشعار للطالب بالرفض
        if ($levelException->status !== LevelExceptionStatus::IN_REVIEW) {
            throw ValidationException::withMessages([
                'status' => 'You cannot reject',
            ]);
        }
        $levelException->update([
            'status' => LevelExceptionStatus::REJECTED,
            'executed_by' => $user->id,
            'executed_at' => now(),
            'review_note' => $data['review_note']
        ]);

        $student = User::find($levelException->user_id);
        if ($student) {
                SendNotificationJob::dispatch(
                [$student->id],
                'Level Exception reject',
                 'Your request to access the level: ' . $levelException->requestedLevel->name_en . ' has been rejected.',
                [
                    'level_exception_id' => $levelException->id,
                ],
                'level-exception-reject'
            );
        }
        return $levelException;
    }
}
