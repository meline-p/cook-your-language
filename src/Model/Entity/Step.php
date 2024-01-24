<?php

namespace App\Model\Entity;

/**
 * Class representing Post entity.
 */
class Step
{
    // properties
    public $id;
    public $recipe_id;
    public $action_id;

    public function fromSql($row)
    {
        $this->id = $row['id'];
        $this->recipe_id = $row['recipe_id'];
        $this->action_id = $row['action_id'];
    }

    public function init($recipe_id, $action_id)
    {
        $this->recipe_id = $recipe_id;
        $this->action_id = $action_id;
    }

    public function update($recipe_id, $action_id)
    {
        $this->recipe_id = $recipe_id;
        $this->action_id = $action_id;
    }
}
