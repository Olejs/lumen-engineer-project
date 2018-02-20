<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 12.01.18
 * Time: 18:22
 */

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Guests extends Eloquent
{

    protected $collection = 'conference_guests';


    /**
     * Inserting data to conferecne_guests collection
     *
     * @param $title
     * @param $name
     * @param $surname
     * @param $affiliation
     * @param $email
     * @param $address
     * @param $phoneNumber
     * @param $whoAreYou
     * @param $lbo1 - additional info #1
     * @param $lbo2 - additional info #2
     */
    public function insert($title, $name, $surname, $affiliation, $email, $state, $address, $phoneNumber, $whoAreYou, $lbo1, $lbo2, $sections)
    {
        $guest = new Guests;
        $guest->title = $title;
        $guest->name = $name;
        $guest->surname = $surname;
        $guest->affiliation = $affiliation;
        $guest->email = $email;
        $guest->state = $state;
        $guest->roles = array('participant');
        $guest->address = $address;
        $guest->phoneNumber = $phoneNumber;
        $guest->whoAreYou = $whoAreYou;
        $guest->lbo1 = $lbo1;
        $guest->lbo2 = $lbo2;
        $guest->sections = $sections;
        $guest->save();
    }

    /**
     *
     * Set user state
     *
     * @param $id
     * @param $state
     * @return mixed
     */
    public function changeStatus($id, $state){
        $guest = $this::where('_id', $id);
        $guest->update(['state' => $state]);
        return $id;
    }

    /**
     * get all users
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll(){
        return $this::all();
    }

    /**
     * Delete user by id
     *
     * @param $id
     */
    public function deleteById($id){
        $guest = $this::where('_id', $id);
        $guest->delete();
    }
}
