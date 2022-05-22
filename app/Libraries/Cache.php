<?php

namespace App\Libraries;

class Cache
{
    public static function getSet(string $key, \Closure $func)
    {
        return \Illuminate\Support\Facades\Cache::remember($key, env('CACHE_TTL'), $func);
    }

    public static function delete(string $key)
    {
        return \Illuminate\Support\Facades\Cache::delete($key);
    }
}
