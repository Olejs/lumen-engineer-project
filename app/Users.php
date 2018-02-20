<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as Auth;

class Users extends Eloquent implements JWTSubject, Auth
{

    protected $collection = 'conference_users';

    /**
     * @return mixed
     */
    public function getOrganizers(){
           return self::where('roles', 'organizer')->get();
    }

    /**
     * @param $title
     * @param $name
     * @param $surname
     * @param $affiliation
     * @param $email
     * @param $password
     * @param $address
     * @param $phoneNumber
     */
    public function insert($title, $name, $surname, $affiliation, $email, $password, $address, $phoneNumber) {

        $user = new Users;
        $user->title = $title;
        $user->name = $name;
        $user->surname = $surname;
        $user->affiliation = $affiliation;
        $user->email = $email;
        $user->roles = array();
        $user->password = app('hash')->make($password);
        $user->address = $address;
        $user->phoneNumber = $phoneNumber;
        $user->save();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ['email' => $this->email];
    }

    /**
     * Determine if the entity has a given ability.
     *
     * @param  string $ability
     * @param  array|mixed $arguments
     * @return bool
     */
    public function can($ability, $arguments = [])
    {
//        die('can');
        // TODO: Implement can() method.
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
//        die('getAuthIdentifier');
        // TODO: Implement getAuthIdentifier() method.
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
//        die('getRememberToken');
        // TODO: Implement getRememberToken() method.
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
//        die('setRememberToken');
        // TODO: Implement setRememberToken() method.
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
//        die('getRememberTokenName');
        // TODO: Implement getRememberTokenName() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getUsers(){
            return self::all();
    }

    /**
     * @param $_id
     */
    public function deleteUserByID($_id){
        Users::destroy($_id);
    }

}
