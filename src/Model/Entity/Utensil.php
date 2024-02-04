<?php

namespace App\Model\Entity;

/**
 * Class representing Post entity.
 */
class Utensil
{
    // properties
    public $id;
    public $en;
    public $fr;
    public $es;

    public function fromSql($row)
    {
        $this->id = $row['id'];
        $this->en = $row['en'];
        $this->fr = $row['fr'];
        $this->es = $row['es'];
    }

    public function init($en, $fr, $es)
    {
        $this->en = $en;
        $this->fr = $fr;
        $this->es = $es;
    }

    public function update($en, $fr, $es)
    {
        $this->en = $en;
        $this->fr = $fr;
        $this->es = $es;
    }
}
