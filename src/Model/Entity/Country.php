<?php

namespace App\Model\Entity;

/**
 * Class representing Post entity.
 */
class Country
{
    // properties
    public $id;
    public $flag;
    public $en;
    public $fr;
    public $es;

    public function fromSql($row)
    {
        $this->id = $row['id'];
        $this->flag = $row['flag'];
        $this->en = $row['en'];
        $this->fr = $row['fr'];
        $this->es = $row['es'];
    }

    public function init($flag, $en, $fr, $es)
    {
        $this->flag = $flag;
        $this->en = $en;
        $this->fr = $fr;
        $this->es = $es;
    }

    public function update($flag, $en, $fr, $es)
    {
        $this->flag = $flag;
        $this->en = $en;
        $this->fr = $fr;
        $this->es = $es;
    }
}
