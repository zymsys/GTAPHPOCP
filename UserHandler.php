<?php

require_once('BaseHandler.php');

class UserHandler extends BaseHandler
{
    public function get()
    {
        $path = $_SERVER['PATH_INFO'];
        if ($path == '/')
        {
            $this->responseData = array();
            $statement = $this->pdo->prepare("SELECT * FROM `user`");
            $statement->execute();
            foreach($statement->fetchAll() as $row)
            {
                $this->responseData[] = array(
                    'id'=>$row['id'],
                    'first'=>$row['first'],
                    'last'=>$row['last']
                );
            }
        }
        else
        {
            $requestId = substr($path, 1);
            $statement = $this->pdo->prepare("SELECT * FROM `user` WHERE `id` = ?");
            $statement->execute(array($requestId));
            $row = $statement->fetch();
            if ($row === false)
            {
                $this->responseData->status = 'error';
                $this->responseData->message = 'Unable to load user: '.$requestId;
            }
            else
            {
                $this->responseData->id = $row['id'];
                $this->responseData->first = $row['first'];
                $this->responseData->last = $row['last'];
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
}
