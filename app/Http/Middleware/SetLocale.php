<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App;
use Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Check session for locale, default to 'en'
        $locale = session('locale', 'en');
        App::setLocale($locale);

        return $next($request);
    }
}
