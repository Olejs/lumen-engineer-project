<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 30.12.17
 * Time: 09:58
 */

namespace App\Http\Middleware;
use Closure;
use Tymon\JWTAuth\JWTAuth;



class CheckingOrganizerRole
{
    /**
     * CatchingExceptions constructor.
     * @param Auth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function handle($request, Closure $next, $guard = null)
    {
            $user = $this->auth->user();
            if(!in_array('organizer',$user->roles)){
                return response()->json(array('Does not have ORGANIZER role'), 401);
            } else {
                return $next($request);
            }
    }
}
