<?php

require_once('BaseHandler.php');

class UserHandler extends BaseHandler
{
    public function get()
    {
        $path = $_SERVER['PATH_INFO'];
        if ($path == '/')
        {
            $responseData = array();
            $statement = $this->pdo->prepare("SELECT * FROM `user`");
            $statement->execute();
            foreach($statement->fetchAll() as $row)
            {
                $responseData[] = array(
                    'id'=>$row['id'],
                    'first'=>$row['first'],
                    'last'=>$row['last']
                );
            }
            echo json_encode($responseData);
        }
        else
        {
            $requestId = substr($path, 1);
            $responseData = new stdClass();
            $statement = $this->pdo->prepare("SELECT * FROM `user` WHERE `id` = ?");
            $statement->execute(array($requestId));
            $row = $statement->fetch();
            if ($row === false)
            {
                $responseData->status = 'error';
                $responseData->message = 'Unable to load user: '.$requestId;
            }
            else
            {
                $responseData->id = $row['id'];
                $responseData->first = $row['first'];
                $responseData->last = $row['last'];
            }
            echo json_encode($responseData);
        }
    }

    function post()
    {
        $statement = $this->pdo->prepare("INSERT INTO `user` (`id`, `first`, `last`) VALUES (?, ?, ?)");
        $result = $statement->execute(array($this->postData['id'], $this->postData['first'], $this->postData['last']));
        $responseData = new stdClass();
        $responseData->status = $result ? 'ok' : 'error';
        echo json_encode($responseData);
    }

    function put()
    {
        $updateUserId = substr($_SERVER['PATH_INFO'], 1);
        $statement = $this->pdo->prepare("UPDATE `user` SET `first`=?, `last`=? WHERE `id`=?");
        $result = $statement->execute(array($this->postData['first'], $this->postData['last'], $updateUserId));
        $responseData = new stdClass();
        $responseData->status = $result ? 'ok' : 'error';
        echo json_encode($responseData);
    }

    function delete()
    {
        $deleteId = substr($_SERVER['PATH_INFO'], 1);
        $statement = $this->pdo->prepare("DELETE FROM `user` WHERE `id`=?");
        $result = $statement->execute(array($deleteId));
        $responseData = new stdClass();
        $responseData->status = $result ? 'ok' : 'error';
        echo json_encode($responseData);
    }
}
