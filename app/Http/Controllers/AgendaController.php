<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 03.02.18
 * Time: 22:37
 */

namespace App\Http\Controllers;

use Validator;
use App\Http\Validators\ApiValidator;
use Illuminate\Http\Request;
use App\Agenda;

class AgendaController extends Controller
{
    /**
     * @param Agenda $agenda
     * @param Request $request
     * @param $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Agenda $agenda, Request $request, $eventId){
        $arrayToValidate = array('eventId' => $eventId);

        $validatedData = Validator::make($arrayToValidate, [
            'eventId' => 'exists:conference_events,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }
        $validatedData = Validator::make($request->all(), [
//            'sectionId' => 'exists:conference_sections,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        };

        if($request->sectionId == null){
            $agenda->remove($eventId);
        } else {
            $agenda->insert($eventId, $request->sectionId);
        }
    }

    /**
     * @param Agenda $agenda
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Agenda $agenda){

        $array = $agenda->all();
        $json = [];

        foreach ($array as $row){
            $json[] = [
                'eventId' => $row->event,
                'sectionId' => $row->section
            ];
        }

        return response()->json($json, 200);
    }
}