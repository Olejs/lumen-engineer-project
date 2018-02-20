<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 28.12.17
 * Time: 17:12
 */

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\JWTGuard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validators\ApiValidator;
use Validator;

class AuthController extends Controller
{
    protected $jwt;

    /**
     * AuthController constructor.
     * @param JWTAuth $jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

        $validateData = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required'
        ], ApiValidator::getMessages());

        if($validateData->fails()){
            $errors = $validateData->messages();
            return response()->json(ApiValidator::response(array(), $errors), 400);
        }
        try {

            if (!$token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(ApiValidator::response(array(), array('Bad credentials')), 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(ApiValidator::response(array(), array('Token expired')), 400);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(ApiValidator::response(array(), array('Token invalid')), 400);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(ApiValidator::response(array(), array('Token absent' => $e->getMessage())), 401);

        }

        return response()->json(compact('token'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(){
        try {
            $token = $this->jwt->parseToken();
            $token = $this->jwt->refresh($token);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(ApiValidator::response(array(), array($e->getMessage())), 401);
        }

        return response()->json(compact('token'));
    }
}