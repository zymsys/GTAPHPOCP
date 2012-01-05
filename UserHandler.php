<?php

require_once('BaseHandler.php');
require_once('UserModel.php');

class UserHandler extends BaseHandler
{
    /**
     * @return BaseMapper
     */
    protected function getMapper()
    {
        if (!isset($this->mapper))
        {
            $this->mapper = new BaseMapper($this->pdo, 'UserModel', 'user', 'id');
        }
        return $this->mapper;
    }

    protected function modelFromRequest()
    {
        return new UserModel($this->postData['id'], $this->postData['first'], $this->postData['last']);
    }
}
