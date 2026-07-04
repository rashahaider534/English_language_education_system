<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = ['en', 'ar'];
        $locale = $request->header('Accept-Language');
        if (! in_array($locale, $availableLocales)) {
            $locale = config('app.locale');
        }
        app()->setLocale($locale);
        return $next($request);
    }
}
