<?php

require_once('IEncoder.php');

class JSONEncoder implements IEncoder
{
    public function encode($data)
    {
        return json_encode($data);
    }
}