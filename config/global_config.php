<?php
include(dirname(__FILE__) . '/../api/lib/autoload.php');
include('server_config.php');
$mysql_link = mysqli_connect('127.0.0.1', MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
DAO::init($mysql_link);


