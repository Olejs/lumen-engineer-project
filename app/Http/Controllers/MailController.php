<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 08.12.17
 * Time: 10:12
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;
use App\Http\Validators\ApiValidator;
use App\Users;

class MailController extends Controller
{
    public function send(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'msgBody' => 'required|max:10000',
            'to.emails' => 'required_without:to.group|array',
            'to.emails.*' => 'email|distinct'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {

            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $title = $request->title;
        $msgBody = $request->msgBody;
        $to = $request->to;

        Mail::raw('', function ($msg) use ($title, $msgBody, $to) {
            $msg->to($to['emails']);
            $msg->from([env('MAIL_USERNAME')]);
            $msg->subject($title);
            $msg->setBody($msgBody, 'text/html');
        });
    }

    public function listing(Request $request, Users $users){
        $validatedData = Validator::make($request->all(), [
            'input' => 'required|string|min:3'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $input = $request->input;

        $output = Users::where('email', 'like', '%'.$input.'%')->get(['email']);

        return response()->json(['emails' => $output]);
    }
}