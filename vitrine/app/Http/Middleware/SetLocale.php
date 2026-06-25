<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->cookie('locale', 'fr');
        app()->setLocale(in_array($locale, ['fr', 'en'], true) ? $locale : 'fr');

        return $next($request);
    }
}
