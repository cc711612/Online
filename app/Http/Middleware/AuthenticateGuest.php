<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthenticateGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $fakeUser = new User();
        $fakeUser->virtual_client = true;
        $fakeUser->id = rand(100,1000);
        $fakeUser->name = Str::random('10');

        $request->merge(['user' => $fakeUser]);
        $request->setUserResolver(function () use ($fakeUser) {
            return $fakeUser;
        });
        return $next($request);
    }
}
