<?php

namespace App\Model\Entity;

/**
 * Class representing Post entity.
 */
class RecipeIngredient
{
    // properties
    public $recipe_id;
    public $quantity_id;
    public $ingredient_id;

    public function fromSql($row)
    {
        $this->recipe_id = $row['recipe_id'];
        $this->quantity_id = $row['quantity_id'];
        $this->ingredient_id = $row['ingredient_id'];
    }

    public function init($recipe_id, $quantity_id, $ingredient_id)
    {
        $this->recipe_id = $recipe_id;
        $this->quantity_id = $quantity_id;
        $this->ingredient_id = $ingredient_id;
    }

    public function update($recipe_id, $quantity_id, $ingredient_id)
    {
        $this->recipe_id = $recipe_id;
        $this->quantity_id = $quantity_id;
        $this->ingredient_id = $ingredient_id;
    }
}
