<?php

require_once('UserHandler.php');

header('Content-type: application/json');
$handler = new UserHandler(new PDO('sqlite:/Users/vic/php/OCP/db/db.sqlite'));
$handler->processRequest();