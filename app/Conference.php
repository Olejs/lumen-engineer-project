<?php
/**
 * Created by PhpStorm.
 * Users: olejs
 * Date: 03.11.17
 * Time: 14:33
 */

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Conference extends Eloquent
{
    protected $collection = 'conference';

    /**
     * @return bool
     */
    public function exist(){
        return boolval(self::count());
    }

    /**
     * @return mixed
     */
    public function getConference(){
        return self::first();
    }

    /**
     * @param $request
     */
    public function init($request){
        $this->name      = $request->input('name');
        $this->startDate = $request->input('startDate');
        $this->endDate   = $request->input('endDate');
        $this->price     = $request->input('price');
        $this->registrationOpen = true;
        $this->save();
    }

    /**
     * @param $request
     */
    public function edit($request){
        $conference = $this::first();
        $conference->name       = $request->input('name');
        $conference->startDate  = $request->input('startDate');
        $conference->endDate    = $request->input('endDate');
        $conference->price            = $request->input('price');
        $conference->save();
    }

    /**
     * @param $registrationOpen
     * @param $submissionOpen
     */
    public function setStatuses($registrationOpen){
        $conference = $this::first();
        $conference->registrationOpen = $registrationOpen;
        $conference->save();
    }
}