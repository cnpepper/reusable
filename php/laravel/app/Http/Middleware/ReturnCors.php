<?php

namespace App\Http\Middleware;

use Closure;

class ReturnCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        var_dump('我是一个中间件:我在请求之后执行'.print_r($response,true));
        return $response;
    }
}
