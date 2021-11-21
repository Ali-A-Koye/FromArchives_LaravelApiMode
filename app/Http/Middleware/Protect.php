<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;

class Protect
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

        $user = Auth::check();

        if (!$user) {
            // redirect page or error.
            return response()->json([
                'Result' => 'Fail',
                'Error' => 'you are not logged in'
            ]);
        }

        return $next($request);
    }
}
