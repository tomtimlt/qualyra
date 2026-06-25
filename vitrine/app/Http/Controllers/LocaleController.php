<?php

namespace App\Http\Controllers;

class LocaleController extends Controller
{
    public function __invoke(string $locale)
    {
        return back()->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}
