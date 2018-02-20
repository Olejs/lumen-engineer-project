<?php
/**
 * Created by PhpStorm.
 * Users: root
 * Date: 15.11.17
 * Time: 13:59
 */
namespace App\Http\Controllers;

use App\Http\Validators\ApiValidator;
use App\Invitations;
use Validator;
use Illuminate\Http\Request;

class InvitationsController extends Controller
{

    /**
     * @param Request $request
     * @param Invitations $invitations
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request, Invitations $invitations)
    {
        $validatedData = Validator::make($request->all(), [
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|string|distinct|min:3|email'
        ], ApiValidator::getMessages());

        if ($validatedData->fails()) {
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $invitations->saveEmails($request->emails);
    }
}