<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show(Request $request)
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);
        $request->session()->put('captcha_sum', $a + $b);

        return view('contact', compact('a', 'b'));
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('success', __('contact.success'));
        }

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
            'captcha' => ['required'],
            'consent' => ['accepted'],
        ]);

        if ((int) $request->input('captcha') !== (int) $request->session()->pull('captcha_sum')) {
            return back()->withInput()->withErrors(['captcha' => __('contact.error_captcha')]);
        }

        ContactMessage::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'message' => $data['message'],
            'ip'      => $request->ip(),
            'locale'  => app()->getLocale(),
        ]);

        return redirect()->route('contact')->with('success', __('contact.success'));
    }
}
