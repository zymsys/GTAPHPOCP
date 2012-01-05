<?php

require_once('BaseMapper.php');

class BaseHandler
{
    protected $pdo;
    protected $postData;
    protected $responseData;

    public function __construct($pdo, $postdata = null)
    {
        $this->pdo = $pdo;
        if (is_null($postdata))
        {
                $this->postData = array();
                $raw = '';
                $fd = fopen("php://input", "r");
                while ($data = fread($fd, 1024))
                  $raw .= $data;
                parse_str($raw, $this->postData);
        }
        else
        {
            $this->postData = $postdata;
        }
    }

    public function processRequest()
    {
        $this->responseData = new stdClass();
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (method_exists($this, $method))
        {
            call_user_func(array($this, $method));
        }
        echo json_encode($this->responseData);
    }
}