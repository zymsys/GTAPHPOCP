<?php

require_once('BookHandler.php');

header('Content-type: application/json');
$handler = new BookHandler(new PDO('sqlite:db/db.sqlite'));
$handler->processRequest();