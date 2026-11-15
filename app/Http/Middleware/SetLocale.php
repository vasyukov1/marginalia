<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', 'ru');
        app()->setLocale(in_array($locale, ['ru', 'en'], true) ? $locale : 'ru');

        return $next($request);
    }
}
