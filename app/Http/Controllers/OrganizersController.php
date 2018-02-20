<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 18.11.17
 * Time: 23:14
 */

namespace App\Http\Controllers;

use App\Invitations;
use App\Users;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Validator;
use App\Http\Validators\ApiValidator;

class OrganizersController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function lists(Users $users){
        $organizers = $users->getOrganizers();
        $organizersArray = array();

        foreach ($organizers as $organizer){
            $organizersArray[] = array(
                'title' => $organizer->title,
                'name' => $organizer->name,
                'surname' => $organizer->surname,
                'affiliation' => $organizer->affiliation,
                'email' => $organizer->email,
                'UUID' => $organizer->_id
            );
        }
        
        return response()->json($organizersArray);
    }

    /**
     * @param Users $users
     * @param Invitations $invitations
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Users $users, Invitations $invitations, Request $request, JWTAuth $auth){
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|email',
            'invitationToken' => 'required'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {

            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $email = $request->email;
        $token = $request->invitationToken;

        $confirmedUsers = $invitations::where('confirmed', '=', true)->where('email', $email)->get();

        if(!$confirmedUsers->isEmpty()){

            $errors = array('User already in committee');
            return response()->json(ApiValidator::response(array(), $errors), 400);

        } else {
            $user = $invitations::where('hash', $token)->where('email', $email)->first();

            if(!$user){

                $errors = array('User is not invited');
                return response()->json(ApiValidator::response(array(), $errors), 400);

            } else {
                //aktualizacja kolekcji invitlations
                $user->confirmed = true;
                $user->save();
                //aktualizacja kolekcji users
                $userDocument = $users::where('email', $email)->get()->first();
                $userDocument->roles = array('organizer');
                $userDocument->save();
            }
        }
    }
}
