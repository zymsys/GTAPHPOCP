<?php

class UserModel
{
    public $id;
    public $first;
    public $last;

    function __construct($id, $first, $last) {
        $this->id = $id;
        $this->first = $first;
        $this->last = $last;
    }
}