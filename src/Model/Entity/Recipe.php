<?php

namespace App\Model\Entity;

/**
 * Class representing Post entity.
 */
class Recipe
{
    // properties
    public $id;
    public $name_id;
    public $time_id;
    public $number_of_people;

    public function fromSql($row)
    {
        $this->id = $row['id'];
        $this->name_id = $row['name_id'];
        $this->time_id = $row['time_id'];
        $this->number_of_people = $row['number_of_people'];
    }

    public function init($name_id, $time_id, $number_of_people)
    {
        $this->name_id = $name_id;
        $this->time_id = $time_id;
        $this->number_of_people = $number_of_people;
    }

    public function update($name_id, $time_id, $number_of_people)
    {
        $this->name_id = $name_id;
        $this->time_id = $time_id;
        $this->number_of_people = $number_of_people;
    }
}
