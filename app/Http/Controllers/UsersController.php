<?php
/**
 * Created by PhpStorm.
 * User: olejs
<<<<<<< HEAD
 * Date: 18.12.17
 * Time: 22:16
=======
 * Date: 27.12.17
 * Time: 20:02
>>>>>>> 13-registration
 */

namespace App\Http\Controllers;

use App\Users;
use Validator;
use App\Http\Validators\ApiValidator;
use App\Conference;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * @param Users $user
     * @param Request $request
     */
    public function register(Users $user, Request $request) {

        $validatedData = Validator::make($request->all(), [
            'title' => 'required',
            'name' => 'required|max:50',
            'surname' => 'required|max:50',
            'affiliation' => 'required|max:50',
            'email' => 'required|email|max:50|unique:conference_users,email',
            'password' => 'required|max:50',
            'passwordConfirm' => 'same:password',
            'address' => 'string',
            'phoneNumber' => 'integer'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $title = $request->title;
        $name = $request->name;
        $surname = $request->surname;
        $affiliation = $request->affiliation;
        $email = $request->email;
        $password = $request->password;
        $address = Request::hasMacro('address') ? $request->address : null;
        $phoneNumber = Request::hasMacro('phoneNumber') ? $request->phoneNumber : null;

        $user->insert($title, $name, $surname, $affiliation, $email, $password, $address, $phoneNumber);
    }

    /**
     * @param Request $request
     * @param Users $users
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, Users $users, $userId){
        $array = array(
            $request->all(),
            'userId' => $userId
        );

        $validatedData = Validator::make($array, [
            'userId' => 'required|exists:conference_users,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        if(Users::count()<2){
            return response()->json(ApiValidator::response(array(),array("Cannot remove last organizer")));
        }

        $users->deleteUserByID($userId);
    }
}



