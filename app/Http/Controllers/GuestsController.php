<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 12.01.18
 * Time: 18:37
 */

namespace App\Http\Controllers;


use App\Guests;
use App\Http\Validators\ApiValidator;
use function FastRoute\TestFixtures\empty_options_cached;
use Illuminate\Http\Request;
use Validator;
use Maatwebsite\Excel\Facades\Excel;

class GuestsController
{
    /**
     * @param Guests $guest
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Guests $guest, Request $request) {

        $validatedData = Validator::make($request->all(), [
            'title' => 'required',
            'name' => 'required|max:50',
            'surname' => 'required|max:50',
            'affiliation' => 'required|max:50',
            'email' => 'required|email|max:50|unique:conference_guests,email',
            'address' => 'string',
            'phoneNumber' => 'integer',
            'whoAreYou' => 'string|max:1000',
            'lbo1' => 'string|max:1000',
            'lbo2' => 'string|max:1000',
            'sections' => 'required|array|min:0',
            'sections.*' => 'string|distinct|exists:conference_sections,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $title = $request->title;
        $name = $request->name;
        $surname = $request->surname;
        $affiliation = $request->affiliation;
        $email = $request->email;
        $state = 'pending';
        $address = empty($request->input('address')) ? null : $request->address;
        $phoneNumber = empty($request->input('phoneNumber')) ? null : $request->phoneNumber;
        $whoAreYou = empty($request->input('whoAreYou')) ? null : $request->whoAreYou;
        $lbo1 = empty($request->input('lbo1')) ? null : $request->lbo1;
        $lbo2 = empty($request->input('lbo2')) ? null : $request->lbo2;
        $sections = empty($request->input('sections')) ? null : $request->sections;
        $guest->insert($title, $name, $surname, $affiliation, $email, $state, $address, $phoneNumber, $whoAreYou, $lbo1, $lbo2, $sections);
    }

    /**
     * @param Request $request
     * @param Guests $guests
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setState(Request $request, Guests $guests, $id){

        $arrayToValidate = array('id' => $id);

        $validatedData = Validator::make($arrayToValidate, [
            'id' => 'exists:conference_guests,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $validatedData = Validator::make($request->all(), [
            'state' => 'required|in:pending,accepted,declined'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $id = $guests->changeStatus($id, $request->state);
        return response()->json(array('id' => $id) ,200);
    }

    /**
     * @param Guests $guests
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAll(Guests $guests){

        $guestsList = $guests->getAll();
        $output = array();
        foreach($guestsList as $guest)
        {
            $element = array();
            $element['id'] = $guest['_id'];
            $element['title'] = $guest['title'];
            $element['name'] = $guest['name'];
            $element['surname'] = $guest['surname'];
            $element['affiliation'] = $guest['affiliation'];
            $element['email'] = $guest['email'];
            $element['state'] = $guest['state'];
            $element['address'] = $guest['address'];
            $element['phoneNumber'] = $guest['phoneNumber'];
            $element['whoAreYou'] = $guest['whoAreYou'];
            $element['lbo1'] = $guest['lbo1'];
            $element['lbo2'] = $guest['lbo2'];

            $output[] = $element;
        }

        return response()->json($output,200);
    }

    /**
     * @param Guests $guests
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Guests $guests, $id){

        $arrayToValidate = array('id' => $id);

        $validatedData = Validator::make($arrayToValidate, [
            'id' => 'exists:conference_guests,_id'
        ], ApiValidator::getMessages());

        if($validatedData->fails()){
            $errors = $validatedData->messages();
            return response()->json(ApiValidator::response($errors, array()), 400);
        }

        $guests->deleteById($id);
    }

    public function export(Guests $guests){

        $data = $guests->all();

        $result[] = ['Title', 'Name', 'Surname', 'Affiliation', 'Email', 'State'];

        foreach ($data as $row) {
            $result[] = [
                $row['title'],
                $row['name'],
                $row['surname'],
                $row['affiliation'],
                $row['email'],
                $row['state']
            ];
        }

        $data = $result;

        return Excel::create('participants', function($excel) use ($data) {
            $excel->sheet('participants', function($sheet) use ($data)
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