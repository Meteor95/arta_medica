<?php
 
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
class JWTFromCookieMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = Cookie::get('token_device');
        $user_id = Cookie::get('user_id');
        if (!$token) {
            return redirect('403');
        }
        try {
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            $user_id = $user->id;
            $userDetails = $user->detailUserInformation($user_id);
            $request->attributes->set('user_details', $userDetails);
            $request->attributes->set('token_device', $token); 
        } catch (JWTException $e) {
            return redirect('403');
        }
        return $next($request);
    }
}