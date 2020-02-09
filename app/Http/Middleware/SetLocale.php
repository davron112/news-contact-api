<?php

namespace App\Http\Middleware;

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

        $locale = locale()->isSupported($desiredLocale) ? $desiredLocale : locale()->fallback();

        locale()->set($locale);

        return $next($request);
    }
}
