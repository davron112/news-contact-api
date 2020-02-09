<?php

    use Carbon\Carbon;

    /**
     * for active element menu
     */
    if (!function_exists('classActivePath')) {
        function classActivePath($path)
        {
            return \App\Http\Requests\Request::is($path) ? 'active' : '';
        }
    }

    if (!function_exists('clean_slug'))
    {

        /**
         * @param $string
         * @return string
         */
        function clean_slug($string = null)
        {
            if (!$string) {
                $string = \Illuminate\Support\Str::random(10);
            }
            // Replaces all spaces with hyphens.
            $string = str_replace(' ', '-', $string);

            // Removes special chars.
            return \Illuminate\Support\Str::lower(preg_replace('/[^A-Za-z0-9\-]/', '', $string));
        }
    }

    if (! function_exists('array_get')) {
        /**
         * @param $array
         * @param $key
         * @param null $default
         * @return mixed
         */
        function array_get($array, $key, $default = null)
        {
            return \Illuminate\Support\Arr::get($array, $key, $default);
        }
    }

    if (! function_exists('locale')) {
        /**
         * @return mixed
         * @throws \Illuminate\Contracts\Container\BindingResolutionException
         */
        function locale()
        {
            return app()->make(App\Locale::class);
        }
    }
