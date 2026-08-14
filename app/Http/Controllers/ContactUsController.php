<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreContactUsRequest;
use App\Mail\NewContactUsMessage;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function store(StoreContactUsRequest $request)
    {
        $contactMessage = ContactUs::create([
            'user_id' => $request->user()->id,
            'text'    => $request->validated('text'),
        ]);

        Mail::to(config('mail.super_admin_email'))
            ->queue(new NewContactUsMessage($contactMessage));

        return response()->json([
            'message' => 'Your message has been sent successfully.',
        ], 201);
    }
}
