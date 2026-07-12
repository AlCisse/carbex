<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * Supported application locales.
     *
     * @var array<int, string>
     */
    protected array $supportedLocales = ['fr', 'en', 'de'];

    /**
     * Handle an incoming request.
     *
     * Resolves the locale from the `lang` query parameter, then the
     * `Accept-Language` header (API requests are stateless, no session).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang');

        if (! in_array($locale, $this->supportedLocales, true)) {
            $locale = $request->getPreferredLanguage($this->supportedLocales);
        }

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
