<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 22.01.18
 * Time: 00:57
 */

namespace App\Http\Controllers;

use App\Sections;
use Validator;
use App\Http\Validators\ApiValidator;
use App\Conference;
use Illuminate\Http\Request;
use App\Agenda;
use App\Guests;

class SectionsController extends Controller
{

    /**
     * @param Sections $sections
     * @param Request $request
     * @param Conference $conference
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Sections $sections, Request $request, Conference $conference){

        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'path' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'price' => 'required|between:1,999999999',
            'bookCapacity' => 'required|between:0,999999999',
            'bookOver' => 'required|between:0,999999999',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }
        $conference = Conference::first();

        if(
//          $request->startDate < $conference->startDate ||
            $request->startDate > $conference->endDate ||
            $request->endDate > $conference->endDate){

            return response()->json(ApiValidator::response(array(), array("Incorrect date")), 400);
        }

        $name = $request->name;
        $description = $request->description;
        $path = $request->path;
        $location = $request->location;
        $price = $request->price;
        $bookCapacity = $request->bookCapacity;
        $bookOver = $request->bookOver;
        $startDate = $request->startDate;
        $endDate = $request->endDate;


        $sections->insert($name, $description, $path, $location, $price, $bookCapacity, $bookOver, $startDate, $endDate);
    }

    /**
     * @param Sections $sections
     * @param Request $request
     * @param Conference $conference
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Sections $sections, Request $request, Conference $conference, $id){

        $arrayToValidate = array('id' => $id);

        $validatedData = Validator::make($arrayToValidate, [
            'id' => 'exists:conference_sections,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'path' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'price' => 'required|between:1,999999999',
            'bookCapacity' => 'between:0,999999999',
            'bookOver' => 'required|between:0,999999999',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $name = $request->name;
        $description = $request->description;
        $path = $request->path;
        $location = $request->location;
        $price = $request->price;
        $bookCapacity = $request->bookCapacity;
        $bookOver = $request->bookOver;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $sections->updateSection($id, $name, $description, $path, $location, $price, $bookCapacity, $bookOver, $startDate, $endDate);
    }

    /**
     * @param Sections $sections
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Sections $sections){

        $array = $sections->all()->sortByDesc('startDate');
        $json = [];

        foreach($array as $row){

            $seatsTaken = Guests::where('sections', $row->_id)->whereIn('state', ['pending', 'accepted'])->count();

            $json[] = [
                'id' => $row->_id,
                'name' => $row->name,
                'description' => $row->description,
                'path' => $row->path,
                'location' => $row->location,
                'price' => $row->price,
                'bookCapacity' => $row->bookCapacity,
                'bookOver' => $row->bookOver,
                'startDate' => $row->startDate,
                'endDate' => $row->endDate,
                'seatsTaken' => $seatsTaken
            ];
        }

        return response()->json($json, 200);
    }

    /**
     * @param Sections $sections
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Sections $sections, $id){

        $arrayToValidate = array('id' => $id);

        $validatedData = Validator::make($arrayToValidate, [
            'id' => 'exists:conference_sections,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }
        Agenda::where('section', $id)->update(['section' => null]);

        Sections::destroy($id);
    }
}

