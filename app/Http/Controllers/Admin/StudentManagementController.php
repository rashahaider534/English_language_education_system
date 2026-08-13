<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Management\StudentManagementService;
class StudentManagementController extends Controller
{
     public function __construct(
        private StudentManagementService $studentManagementService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $levelId = $request->query('level');

        $data = $this->studentManagementService->getIndexData(
            $search,
            $levelId
        );

        return view('admin.students.index', [
            'students' => $data['students'],
            'search' => $search,
            'levelId' => $levelId,
            'levels' => $data['levels'],
            'totalCount' => $data['totalCount'],
            'levelDistribution' => $data['levelDistribution'],
            'levelDistributionMax' => $data['levelDistributionMax'],
        ]);
    }


}
