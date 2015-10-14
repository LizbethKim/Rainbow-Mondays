<?php
header('content-type: application/json');
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
include(dirname(__FILE__) . '/../../config/global_config.php');

$uri = explode("/", $_SERVER['REQUEST_URI']);
$methodName = $uri[count($uri) - 1] . 'Action';
$controller = new Controller();
if(method_exists($controller, $methodName)) {
    echo json_encode($controller->{$methodName}());
} else {
    echo 'Method not found';
}
