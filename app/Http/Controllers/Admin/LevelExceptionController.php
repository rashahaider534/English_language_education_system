<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LevelException\UpdateLevelExceptionRequest;
use App\Models\LevelException;
use Illuminate\Http\Request;
use App\Services\Level_Exception\AdminLevelExceptionService;

class LevelExceptionController extends Controller
{
    public function __construct(
        private AdminLevelExceptionService $service
    ) {}

    public function index(Request $request)
    {
        $levelExceptions = $this->service->index( $request->status);
        return view('admin.level-exceptions.index', compact('levelExceptions'));
    }

    public function show(LevelException $levelException)
    {
        $levelException = $this->service->show($levelException);
        return view('admin.level-exceptions.show',compact('levelException'));
    }

    public function startReview(LevelException $levelException)
    {
        $this->authorize('review', $levelException);
        $levelException = $this->service->startReview(auth()->user(), $levelException);
        return redirect()
        ->route('levelException.show', $levelException)
        ->with('success', 'Request moved to review.');
    }

    public function approve(LevelException $levelException, UpdateLevelExceptionRequest $request)
    {
        $this->authorize('approve', $levelException);
        $levelException = $this->service->approve(
            auth()->user(),
            $levelException,
            $request->validated()
        );
        return redirect()
            ->route('levelException.index')
            ->with('success', 'Request approved successfully.');
    }

       public function reject(LevelException $levelException, UpdateLevelExceptionRequest $request)
    {
        $this->authorize('reject', $levelException);
        $levelException = $this->service->reject(
            auth()->user(),
            $levelException,
            $request->validated()
        );
         return redirect()
            ->route('levelException.index')
            ->with('success', 'Request rejected successfully.');
    }
}
