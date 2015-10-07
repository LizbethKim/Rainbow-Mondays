<?php
include('autoload.php');


$mysql_link = mysqli_connect('127.0.0.1', 'rainbowmondays', 'password', 'rainbowmondays');

DAO::init($mysql_link);

define('CONSUMER_KEY', '6CEAB3585FFA4AEDB00EF2CFCEFABEF3');
define('SIGNATURE', '70941C9DF7CF72EFD272387821C4982E');