<?php
/**
 * Created by PhpStorm.
 * User: olejs
 * Date: 21.01.18
 * Time: 00:03
 */

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

use App\Agenda;

class Events extends Eloquent
{

    protected $collection = 'conference_events';

    /**
     * @param $name
     * @param $description
     * @param $type
     * @param $duration
     * @param $presenter
     * @return mixed
     */
    public function insert($name, $description, $type, $duration, $presenter){

        $event = new Events;
        $event->name = $name;
        $event->description = $description;
        $event->type = $type;
        $event->duration = $duration;
        $event->presenter = $presenter;
        $event->save();

        $agenda = new Agenda;
        $agenda->event = $event->_id;
        $agenda->section = null;
        $agenda->save();

        return $event->_id;
    }

    /**
     * @param array $id
     * @param array $name
     * @param $description
     * @param $type
     * @param $duration
     * @param $presenter
     * @return mixed
     */
    public function updateEvent($id, $name, $description, $type, $duration, $presenter){

        $event = $this::where('_id', $id);
        $event->update([
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'duration' => $duration,
            'presenter' => $presenter
        ]);

        return $id;
    }
}