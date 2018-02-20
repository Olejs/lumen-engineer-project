<?php
/**
 * Created by PhpStorm.
 * Users: root
 * Date: 16.11.17
 * Time: 11:37
 */

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Http\Validators\ApiValidator;
use App\Invitations;

class ConfirmationController extends BaseController
{
    /**
     * @param Request $request
     * @param $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request, $hash)
    {
        $invitedPerson = Invitations::where('hash', $hash)->get();

        if($invitedPerson->isEmpty()){

            $errors = array('Incorrect invitation');
            return response()->json(ApiValidator::response(array(), $errors), 404);

        } else {

            $invitedPerson->confirmed = true;
            $invitedPerson->save();

        }
    }
}