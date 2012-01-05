<?php

require_once('IEncoder.php');
require_once('XML/Serializer.php'); //From PEAR

class XMLEncoder implements IEncoder
{
    private $rootName;

    public function __construct($rootName)
    {
        $this->rootName = $rootName;
    }

    public function encode($data)
    {
        $serializer = new XML_Serializer(array(
            'rootName' => $this->rootName,
            'linebreak' => '',
            'defaultTagName' => 'item'
        ));
        $serializer->serialize($data);
        return $serializer->getSerializedData();
    }
}