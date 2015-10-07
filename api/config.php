<?php
include('autoload.php');


$mysql_link = mysqli_connect('127.0.0.1', 'rainbowmondays', 'password', 'rainbowmondays');

DAO::init($mysql_link);

define('CONSUMER_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxxxx');
define('SIGNATURE', 'xxxxxxxxxxxxxxxxxxxxxxxxxx');