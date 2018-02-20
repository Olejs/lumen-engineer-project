<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 21.01.18
 * Time: 00:00
 */

namespace App\Http\Controllers;

use App\Agenda;
use App\Events;
use App\Sections;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Validator;
use App\Http\Validators\ApiValidator;
use App\Conference;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EventsController extends Controller
{
    /**
     * @param Request $request
     * @param Events $events
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request, Events $events){

        $validatedData = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'description' => 'required|string|max:1000',
                'type' => 'required|string',
                'duration' => 'required|integer|between:0,1440',
                'presenter' => 'required|string|max:100'
            ], ApiValidator::getMessages());

        if($validatedData->fails()) {
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $name = $request->name;
        $description = $request->description;
        $type = $request->type;
        $duration = $request->duration;
        $presenter = $request->presenter;

        $id = $events->insert($name, $description, $type, $duration, $presenter);

        return response()->json(['id' => $id], 200);
    }

    /**
     * @param Request $request
     * @param Events $events
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Events $events, $id){

        $arrayToValidate = array('id' => $id);

        $validatedData = Validator::make($arrayToValidate, [
            'id' => 'exists:conference_events,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'description' => 'required|string|max:1000',
            'type' => 'required|string',
            'duration' => 'required|integer|between:0,1440',
            'presenter' => 'required|string|max:100'
        ], ApiValidator::getMessages());

        if($validatedData->fails()) {
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $name = $request->name;
        $description = $request->description;
        $type = $request->type;
        $duration = $request->duration;
        $presenter = $request->presenter;

        $id = $events->updateEvent($id, $name, $description, $type, $duration, $presenter);

        return response()->json(['id' => $id]);
    }

    /**
     * @param Events $events
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Events $events){
        $array = $events->all();
        $json = [];
        foreach($array as $row){
            $json[] = [
                'id' => $row->_id,
                'name' => $row->name,
                'description' => $row->description,
                'type' => $row->type,
                'duration' => $row->duration,
                'presenter' => $row->presenter
            ];
        }

        return response()->json($json, 200);
    }

    /**
     * @param Events $events
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Events $events, $id, Agenda $agenda){

        $arrayToValidate = array('id' => $id);

        $validatedData = Validator::make($arrayToValidate, [
            'id' => 'exists:conference_events,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }
        Agenda::where('event', $id)->delete();

        Events::destroy($id);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Sections $sections, Agenda $agenda, Events $events){

        $data = $sections->all()->sortByDesc('startDate');

        $result[] = ['Name', 'Description', 'Location', 'Price', 'Start date', 'End date'];

        foreach ($data as $row){
            $result[] = [];
            $events = Agenda::where('section', $row['_id'])->get()->toArray();
            $output = [];
            foreach ($events as $event){
                $eventsArr = Events::where('_id', $event['event'])->get()->toArray();
                $output[] = $eventsArr[0];
            }

            $result[] = [
                    $row['name'],
                    $row['description'],
                    $row['location'],
                    $row['price'],
                    $row['startDate'],
                    $row['endDate']
            ];

            if(count($output) > 0){
                $result[] = ['Events'];
            }

            foreach ($output as $el) {
                $result[] = [
                    "",
                    $el['name'],
                    $el['description'],
                    $el['type'],
                    $el['duration'],
                    $el['presenter']
                ];
            }
        }
        $data = $result;

        return Excel::create('agenda', function($excel) use ($data) {
            $excel->sheet('agenda', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
                $sheet->cell('B1', function($cell) {
                    $cell->setValue('');
                });
                $sheet->cell('C1', function($cell) {
                    $cell->setValue('');
                });
                $sheet->cell('D1', function($cell) {
                    $cell->setValue('');
                });
                $sheet->cell('E1', function($cell) {
                    $cell->setValue('');
                });
                $sheet->cell('F1', function($cell) {
                    $cell->setValue('');
                });
            });
        })->download('csv');
    }
}