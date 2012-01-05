<?php

require_once('BaseMapper.php');
require_once('JSONEncoder.php');

abstract class BaseHandler
{
    protected $pdo;
    protected $postData;
    protected $responseData;
    protected $encoder;

    /**
     * @abstract
     * @return BaseMapper
     */
    abstract protected function getMapper();
    abstract protected function modelFromRequest();

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
        echo $this->getEncoder()->encode($this->responseData);
    }

    public function get()
    {
        $path = $_SERVER['PATH_INFO'];
        if ($path == '/')
        {
            $this->responseData = $this->getMapper()->fetch();
        }
        else
        {
            $requestId = substr($path, 1);
            $rows = $this->getMapper()->fetch("`id` = ?", array($requestId));
            if (count($rows) == 0)
            {
                $this->responseData->status = 'error';
                $this->responseData->message = 'Unable to load: '.$requestId;
            }
            else
            {
                $this->responseData = $rows[0];
            }
        }
    }

    function post()
    {
        $result = $this->getMapper()->insert($this->modelFromRequest());
        $this->responseData->status = $result ? 'ok' : 'error';
    }

    function put()
    {
        $updateId = substr($_SERVER['PATH_INFO'], 1);
        $result = $this->getMapper()->update($updateId, $this->modelFromRequest());
        $this->responseData->status = $result ? 'ok' : 'error';
    }

    function delete()
    {
        $deleteId = substr($_SERVER['PATH_INFO'], 1);
        $result = $this->getMapper()->delete($deleteId);
        $this->responseData->status = $result ? 'ok' : 'error';
    }

    public function setEncoder($encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @return IEncoder
     */
    public function getEncoder()
    {
        if (!isset($this->encoder))
        {
            $this->encoder = new JSONEncoder();
        }
        return $this->encoder;
    }

}