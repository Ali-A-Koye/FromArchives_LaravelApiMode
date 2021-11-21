<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,...$roles)
    {
    $user = Auth::user();

    if($user->isAdmin())
        return $next($request);



    foreach($roles as $role) {
        // Check if user has the role This check will depend on how your roles are set up
        if( Auth::user()->role()->get('name')->first()->name == $role)
            return $next($request);
    }

    return response()->json([
        'status' =>'fail',
        'data'=> 'You Dont Have permission to access this Route'
    ]);
    }
}
