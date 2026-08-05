<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(): View
    {
        $complaints = ContactUs::with('user')->latest()->paginate(10);

        return view('admin.complaints.index', compact('complaints'));
    }
}
