<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class isAdmin
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
        $user = Auth::user();
        if (empty($user)) {
            return response([ 'Alert' => 'You\'re not logged in!'], 403);}
        if($user['role'] === 'Admin'){
            return $next($request);
        }
        
        return response([ 'Alert' => 'Denied. You should be an Admin!'], 403);

        
    }
}
