<?php
header('content-type: application/json');
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
include('config.php');

$controller = new ListController();
echo json_encode($controller->indexAction());