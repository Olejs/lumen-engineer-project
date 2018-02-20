<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 22.01.18
 * Time: 00:59
 */

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Sections extends Eloquent
{
    protected $collection = 'conference_sections';

    /**
     * @param $name
     * @param $description
     * @param $path
     * @param $location
     * @param $price
     * @param $bookCapacity
     * @param $bookOver
     * @param $startDate
     * @param $endDate
     */
    public function insert($name, $description, $path, $location, $price, $bookCapacity, $bookOver, $startDate, $endDate){

        $section = new Sections;
        $section->name = $name;
        $section->description = $description;
        $section->path = $path;
        $section->location = $location;
        $section->price = $price;
        $section->bookCapacity = $bookCapacity;
        $section->bookOver = $bookOver;
        $section->startDate = $startDate;
        $section->endDate = $endDate;
        $section->save();
    }

    /**
     * @param $id
     * @param $name
     * @param $description
     * @param $path
     * @param $location
     * @param $price
     * @param $bookCapacity
     * @param $bookOver
     * @param $startDate
     * @param $endDate
     */
    public function updateSection($id, $name, $description, $path, $location, $price, $bookCapacity, $bookOver, $startDate, $endDate){

        $section = $this::where('_id', $id);
        $section->update([
            'name' => $name,
            'description' => $description,
            'path' => $path,
            'location' => $location,
            'price' => $price,
            'bookCapacity' => $bookCapacity,
            'bookOver' => $bookOver,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
