<?php
/**
 * Created by PhpStorm.
 * Users: olejs
 * Date: 03.11.17
 * Time: 14:17
 */

namespace App\Http\Controllers;

use Validator;
use App\Http\Validators\ApiValidator;
use App\Conference;
use Illuminate\Http\Request;

class ConferenceController
{
    /**
     * @param Conference $conference
     * @return \Illuminate\Http\JsonResponse
     */
    public function exist(Conference $conference){

        if($conference->exist()) {
            $conference = $conference->getConference();

            $output = array(
                'name'      => $conference->name,
                'startDate' => $conference->startDate,
                'endDate'   => $conference->endDate,
                'registrationOpen' => $conference->registrationOpen,
                'price' => $conference->price
            );

            return response()->json($output, 200);
        } else {

            $error = array('Conference does not exist');
            return response()->json(ApiValidator::response(array(), $error), 404);
        }
    }

    /**
     * @param Request $request
     * @param Conference $conference
     * @return \Illuminate\Http\JsonResponse
     */
    public function init(Request $request, Conference $conference){

        if(!$conference->exist()) {

            //syntax validation
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
                'price' => 'required|numeric|min:0'
            ], ApiValidator::getMessages());

            if($validatedData->fails()) {
                $errors = $validatedData->messages();
                return response()->json(ApiValidator::response($errors, array()), 400);
            }

            $conference->init($request);

        } else {

            $error = array('Conference already exist');
            return response()->json(ApiValidator::response(array(), $error), 400);
        }
    }

    /**
     * @param Request $request
     * @param Conference $conference
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, Conference $conference){
        //syntax validation
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'price' => 'required|numeric|min:0'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {

            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $conference->edit($request);
    }

    /**
     * @param Request $request
     * @param Conference $conference
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(Request $request, Conference $conference){

        $validatedData = Validator::make($request->all(), [
            'registrationOpen' => 'required|boolean'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {

            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $conference->setStatuses($request->registrationOpen);
    }
}
