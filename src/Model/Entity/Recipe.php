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
    public $level_id;
    public $description_id;
    public $category_id;
    public $country_id;
    public $season_id;
    public $lactose;
    public $gluten;
    public $vegetarian;
    public $vegan;
    public $spicy;
    public $created_at;
    public $deleted_at;

    public function fromSql($row)
    {
        $this->id = $row['id'];
        $this->name_id = $row['name_id'];
        $this->time_id = $row['time_id'];
        $this->number_of_people = $row['number_of_people'];
        $this->level_id = $row['level_id'];
        $this->description_id = $row['description_id'];
        $this->category_id = $row['category_id'];
        $this->country_id = $row['country_id'];
        $this->season_id = $row['season_id'];
        $this->lactose = $row['lactose'];
        $this->gluten = $row['gluten'];
        $this->vegetarian = $row['vegetarian'];
        $this->vegan = $row['vegan'];
        $this->spicy = $row['spicy'];
        $this->created_at = $row['created_at'];
        $this->deleted_at = $row['deleted_at'];
    }

    public function init(
        $name_id,
        $time_id,
        $number_of_people,
        $level_id,
        $description_id,
        $category_id,
        $country_id,
        $season_id,
        $lactose,
        $gluten,
        $vegetarian,
        $vegan,
        $spicy,
        $created_at
    ) {
        $this->name_id = $name_id;
        $this->time_id = $time_id;
        $this->number_of_people = $number_of_people;
        $this->level_id = $level_id;
        $this->description_id = $description_id;
        $this->category_id = $category_id;
        $this->country_id = $country_id;
        $this->season_id = $season_id;
        $this->lactose = $lactose;
        $this->gluten = $gluten;
        $this->vegetarian = $vegetarian;
        $this->vegan = $vegan;
        $this->spicy = $spicy;
        $this->created_at = $created_at;
    }

    public function update(
        $name_id,
        $time_id,
        $number_of_people,
        $level_id,
        $description_id,
        $category_id,
        $country_id,
        $season_id,
        $lactose,
        $gluten,
        $vegetarian,
        $vegan,
        $spicy,
        $deleted_at
    ) {
        $this->name_id = $name_id;
        $this->time_id = $time_id;
        $this->number_of_people = $number_of_people;
        $this->level_id = $level_id;
        $this->description_id = $description_id;
        $this->category_id = $category_id;
        $this->country_id = $country_id;
        $this->season_id = $season_id;
        $this->lactose = $lactose;
        $this->gluten = $gluten;
        $this->vegetarian = $vegetarian;
        $this->vegan = $vegan;
        $this->spicy = $spicy;
        $this->deleted_at = $deleted_at;
    }
}
