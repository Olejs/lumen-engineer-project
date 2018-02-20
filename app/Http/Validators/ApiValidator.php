<?php
/**
 * Created by PhpStorm.
 * Users: olejs
 * Date: 07.11.17
 * Time: 20:14
 */
namespace App\Http\Validators;

class ApiValidator
{
    public static $messages = array(
        'required'          => 'The :attribute field is required',
        'date'              => 'Bad date',
        'after'             => 'Invalid :attribute',
        'distinct'          => 'Duplicated value',
        'email'             => 'Invalid email address',
        'string'            => 'The :attribute field must be a string',
        'min'               => 'The :attribute must have more than 3 characters',
        'unique'            => 'The :attribute already exists'
    );

    /**
     * @return array
     */
    public static function getMessages()
    {
        return self::$messages;
    }

    /**
     * @param array $messages
     */
    public static function setMessages($messages)
    {
        self::$messages = $messages;
    }

    /**
     * @param array $fieldErrors
     * @param array $globalErrors
     * @return array
     */
    static function response($fieldErrors, $globalErrors){

        return array(
            'fieldErrors'  => $fieldErrors,
            'globalErrors' => $globalErrors
        );
    }

}