<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => ['required']]);

        $expected = (string) config('vitrine.admin_password');

        if ($expected !== '' && hash_equals($expected, (string) $request->input('password'))) {
            $request->session()->put('is_admin', true);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['password' => 'Mot de passe incorrect.']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $messages = ContactMessage::latest()->simplePaginate(25);

        return view('admin.dashboard', compact('messages'));
    }
}
