<?php

class BookModel
{
    public $id;
    public $user;
    public $name;
    public $author;

    public function __construct($id, $user, $name, $author)
    {
        $this->id = $id;
        $this->user = $user;
        $this->name = $name;
        $this->author = $author;
    }
}
