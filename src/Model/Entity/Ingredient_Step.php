<?php

namespace App\Model\Entity;

/**
 * Class representing Post entity.
 */
class Ingredient_Step
{
    // properties
    public $id;
    public $step_id;
    public $ingredient_id;

    public function fromSql($row)
    {
        $this->id = $row['id'];
        $this->step_id = $row['step_id'];
        $this->ingredient_id = $row['ingredient_id'];
    }

    public function init($step_id, $ingredient_id)
    {
        $this->step_id = $step_id;
        $this->ingredient_id = $ingredient_id;
    }

    public function update($step_id, $ingredient_id)
    {
        $this->step_id = $step_id;
        $this->ingredient_id = $ingredient_id;
    }
}