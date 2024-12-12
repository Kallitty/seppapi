<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

          if (Auth::check()) {
            if (auth()->user()->role_as == 1) { // '1' indicates an admin role
                return $next($request);
            } else {
                return response()->json([
                    'status' => 403,
                    'message' => 'Access Denied! You are not an Admin.',
                ], 403);
            }
        }else{
            return response()->json([
                'status'=>401,
                'message'=>'Kindly Login.',
                
            ]);
        }
        
    }
}
