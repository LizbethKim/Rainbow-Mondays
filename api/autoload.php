<?php
function __autoload($className) {
    if(file_exists($className . '.php')) {
        include($className . '.php');
    }
    return(class_exists($className));
}