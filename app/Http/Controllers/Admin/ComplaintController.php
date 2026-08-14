<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Models\ContactUs;
use Illuminate\View\View;
use App\Mail\ReplyToContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class ComplaintController extends Controller
{
    public function index(): View
    {
        $complaints = ContactUs::with('user')->latest()->paginate(10);

        return view('admin.complaints.index', compact('complaints'));
    }

    public function sendReply(Request $request, ContactUs $contactMessage)
    {
        if (!auth()->user()->role('super-admin','web')) {
             return  throw ValidationException::withMessages([
                'error' => 'لا توجد صلاحيات لتنفيذ هذا الإجراء'
            ]);
        }
        $validated = $request->validate([
            'reply_text' => 'required|string|min:10|max:5000',
        ], [
            'reply_text.required' => 'نص الرد مطلوب',
            'reply_text.min' => 'يجب أن يكون الرد على الأقل 10 أحرف',
            'reply_text.max' => 'لا يمكن أن يتجاوز الرد 5000 حرف',
        ]);

        try {
            if (!$contactMessage->user || !$contactMessage->user->email) {
                return  throw ValidationException::withMessages([
                     'error' => 'المستخدم غير موجود أو البريد الإلكتروني غير محدد',
                ]);
            }
            Mail::to($contactMessage->user->email)
                ->queue(new ReplyToContact($contactMessage, $validated['reply_text']));

           return redirect()->route('admin.complaints.index')
                ->with('success', 'تم إرسال الرد بنجاح للطالب ✅');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إرسال الرد: ' . $e->getMessage())
                ->withInput();
        }
    }

}
