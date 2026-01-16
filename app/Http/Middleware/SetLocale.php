<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for locale in query parameter first
        if ($request->has('lang') && in_array($request->get('lang'), ['fr', 'en', 'de'])) {
            $locale = $request->get('lang');
            session(['locale' => $locale]);
        } else {
            $locale = session('locale', config('app.locale', 'fr'));
        }

        if (in_array($locale, ['fr', 'en', 'de'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
