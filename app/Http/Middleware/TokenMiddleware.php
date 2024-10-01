<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization') ?? $request->query('token');


        if ($token) {
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
                $valid = false;
                $tokenData = PersonalAccessToken::where('token', $token)->first();
                if ($tokenData) {
                    $valid = true;
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Bearer Not Found!',
                    'data'    => []
                ]); 
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Token Not Found!',
                'data'    => []
            ]); 
        }


        if($valid) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
            return $next($request);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized!',
                'data'    => []
            ]); 
        }
    }
}

