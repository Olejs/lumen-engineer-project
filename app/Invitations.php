<?php
/**
 * Created by PhpStorm.
 * Users: root
 * Date: 15.11.17
 * Time: 14:02
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Mail;

class Invitations extends Eloquent
{
    protected $collection = 'invitations';

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll(){
        return self::all();
    }

    /**
     * @param $emails
     */
    public function saveEmails($emails){

        foreach ($emails as $email){

            $emailRecords = self::where('email', $email)->get();
                $conference = new Conference();

                $invitation = new Invitations();
                $invitation->email = $email;
                $hash = $invitation->hash = $this->confirmationHash($email);
                $invitation->confirmed = false;
                $invitation->save();
                $conferenceData = $conference->getConference();
                Mail::raw('', function ($msg) use ($email, $hash, $conferenceData) {
                    $msg->to($email);
                    $msg->from([env('MAIL_USERNAME')]);
                    $msg->subject($conferenceData['name'].' - organizing committee invitation');
                    $msg->setBody(view('invitation', ['hash' => $hash, 'conference' => $conferenceData]), 'text/html');
                });
        }
    }

    /**
     * @param $email
     * @return mixed
     */
    public function confirmationHash($email){
        return md5($email . Carbon::now());
    }
}