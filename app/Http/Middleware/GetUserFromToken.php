<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class GetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

            if (! $token = $this->auth->setRequest($request)->getToken()) {
                return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            }
            try {
                $user = $this->auth->authenticate($token);
              
            } catch (TokenExpiredException $e) {

               return response()->json(['error' => 'token_expired'], 404);


              
            } catch (JWTException $e) {

                 $message = "token_invalid";

                return response()->json(compact('message'), 404);
            }

            if (! $user) { 
                return response()->json(['error' => 'user_not_found'], 404);
            }

            // $this->events->fire('tymon.jwt.valid', $user);

            return $next($request);
       
    }


}
