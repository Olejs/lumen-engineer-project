<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 03.02.18
 * Time: 22:39
 */

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Agenda extends Eloquent
{
    protected $collection = 'conference_agenda';

    /**
     * @param $event
     * @param $section
     */
    public function insert($event, $section){

        $agenda = $this::where('event', $event);
        $agenda->update(['section' => $section]);

    }

    /**
     * @param $event
     */
    public function remove($event){

        $agenda = $this::where('event', $event);
        $agenda->update(['section' => null]);

    }

    /**
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }
}