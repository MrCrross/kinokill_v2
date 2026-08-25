<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available locales the site can be switched between, see LocaleController.
     */
    public const AVAILABLE = ['ru', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (in_array($locale, self::AVAILABLE, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
