<?php

require_once('UserHandler.php');

class Tests extends PHPUnit_Framework_TestCase
{
    private $pdo;

    protected function setUp()
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec("CREATE TABLE `user` (`id` VARCHAR(20) PRIMARY KEY, `first` VARCHAR(20), `last` VARCHAR(30))");
        $this->pdo->exec("INSERT INTO `user` (`id`, `first`, `last`) VALUES ('rrich', 'Richie', 'Rich')");
        $this->pdo->exec("INSERT INTO `user` (`id`, `first`, `last`) VALUES ('dduck', 'Donald', 'Duck')");
    }

    function testIndex()
    {
        $handler = new UserHandler($this->pdo, array());
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/';
        $this->expectOutputString('[' .
            '{"id":"rrich","first":"Richie","last":"Rich"},' .
            '{"id":"dduck","first":"Donald","last":"Duck"}' .
            ']');
        $handler->processRequest();
    }

    function testPostGet()
    {
        //POST new user
        $handler = new UserHandler($this->pdo, array('id'=>'mmouse', 'first'=>'Mickey', 'last'=>'Mouse'));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['PATH_INFO'] = '/';
        $handler->processRequest();

        //GET back new user's info
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/mmouse';
        $this->expectOutputString('{"status":"ok"}{"id":"mmouse","first":"Mickey","last":"Mouse"}');
        $handler->processRequest();
    }

    function testDuplicateUserId()
    {
        $handler = new UserHandler($this->pdo, array('id'=>'dduck', 'first'=>'Daniel', 'last'=>'Duck'));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['PATH_INFO'] = '/';
        $this->expectOutputString('{"status":"error"}');
        $handler->processRequest();
    }

    function testPut()
    {
        $handler = new UserHandler($this->pdo, array('id'=>'rrich', 'first'=>'Richard', 'last'=>'Rich'));
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_SERVER['PATH_INFO'] = '/rrich';
        $this->expectOutputString('{"status":"ok"}');
        $handler->processRequest();
        $row = $this->pdo->query("SELECT `first` FROM `user` WHERE `id` = 'rrich'")->fetch();
        $this->assertEquals('Richard', $row['first']);
    }

    function testDelete()
    {
        $this->pdo->exec("INSERT INTO `user` (`id`, `first`, `last`) VALUES ('delme', 'Delete', 'Me')");
        $handler = new UserHandler($this->pdo, array());
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['PATH_INFO'] = '/delme';
        $this->expectOutputString('{"status":"ok"}');
        $handler->processRequest();
        $result = $this->pdo->query("SELECT `first` FROM `user` WHERE `id` = 'delme'")->fetchAll();
        $this->assertEquals(0, count($result));
    }
}