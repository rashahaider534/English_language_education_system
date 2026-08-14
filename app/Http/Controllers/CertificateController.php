<?php

namespace App\Http\Controllers;

use App\Models\UserLevel;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function getCertificate(UserLevel $userLevel)
    {
        if ($userLevel->user_id !== auth()->id()) {
            abort(403);
        }

        if ($userLevel->status !== 'completed') {
            return response()->json(['error' => 'Level not completed yet.'], 422);
        }

        try {
            $certificate = app(CertificateService::class)->issueForUserLevel($userLevel);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'Certificate generation temporarily failed. Please try again.',
            ], 503);
        }

        return response()->json([
            'download_url' => $certificate->certificate_url,
        ]);
    }

    public function index(Request $request, CertificateService $service)
    {
        return response()->json(
            $service->getStudentCertificates($request->user())
        );
    }
}
