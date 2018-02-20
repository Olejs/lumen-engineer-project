<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 29.01.18
 * Time: 23:21
 */
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Invitations;


class SendInvitation extends Command
{
    protected $name = "send:invitation";

    protected $description = "Send invitation to organizing committee";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(Invitations $invitation){

        $input['email'] = $this->ask('Please enter e-mail address');

        $email = [$input['email']];

        $invitation->saveEmails($email);
    }

}