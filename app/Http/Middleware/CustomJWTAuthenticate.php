<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 30.12.17
 * Time: 09:58
 */

namespace App\Http\Middleware;
use Closure;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use Tymon\JWTAuth\JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Http\Validators\ApiValidator;


class CustomJWTAuthenticate
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
        try{
            $auth = new Authenticate($this->auth);
            $auth->authenticate($request);
            return $next($request);
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(array('Token expired'), 401);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(array('Token invalid'), 401);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(ApiValidator::response(array(), array('Other error: ' => $e->getMessage())), 400);

        } catch (UnauthorizedHttpException $e){

            return response()->json(array('Token error'), 403);

        }
    }
}