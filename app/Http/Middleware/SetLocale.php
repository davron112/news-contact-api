<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;

class SetLocale
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle($request, Closure $next)
    {
        $desiredLocale = $request->segment(3);

        if (!Language::where('short_name', '=', $desiredLocale)->first()) {
            $desiredLocale = config('app.locale');
        }

        app()->setLocale($desiredLocale);

        return $next($request);
    }
}
