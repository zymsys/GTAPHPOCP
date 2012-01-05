<?php

require_once('BaseHandler.php');
require_once('BookModel.php');

class BookHandler extends BaseHandler
{
    /**
     * @return BaseMapper
     */
    protected function getMapper()
    {
        if (!isset($this->mapper))
        {
            $this->mapper = new BaseMapper($this->pdo, 'BookModel', 'book', 'id');
        }
        return $this->mapper;
    }

    protected function modelFromRequest()
    {
        return new BookModel($this->postData['id'], $this->postData['user'], $this->postData['name'],
            $this->postData['author']);
    }
}
