<?php

require_once('BaseHandler.php');
require_once('UserModel.php');

class UserHandler extends BaseHandler
{
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
                $this->responseData->message = 'Unable to load user: '.$requestId;
            }
            else
            {
                $this->responseData = $rows[0];
            }
        }
    }

    function post()
    {
        $statement = $this->pdo->prepare("INSERT INTO `user` (`id`, `first`, `last`) VALUES (?, ?, ?)");
        $result = $statement->execute(array($this->postData['id'], $this->postData['first'], $this->postData['last']));
        $this->responseData->status = $result ? 'ok' : 'error';
    }

    function put()
    {
        $updateUserId = substr($_SERVER['PATH_INFO'], 1);
        $statement = $this->pdo->prepare("UPDATE `user` SET `first`=?, `last`=? WHERE `id`=?");
        $result = $statement->execute(array($this->postData['first'], $this->postData['last'], $updateUserId));
        $this->responseData->status = $result ? 'ok' : 'error';
    }

    function delete()
    {
        $deleteId = substr($_SERVER['PATH_INFO'], 1);
        $statement = $this->pdo->prepare("DELETE FROM `user` WHERE `id`=?");
        $result = $statement->execute(array($deleteId));
        $this->responseData->status = $result ? 'ok' : 'error';
    }

    /**
     * @return BaseMapper
     */
    private function getMapper()
    {
        if (!isset($this->mapper))
        {
            $this->mapper = new BaseMapper($this->pdo, 'UserModel', 'user', 'id');
        }
        return $this->mapper;
    }
}
