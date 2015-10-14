<?php
/**
 * @param $className
 * @return bool
 */
function __autoload($className) {
    $fname = dirname(__FILE__) . '/../' . $className . '.php';
    if(file_exists($fname)) {
        include($fname);
    } else {
        $fname = dirname(__FILE__) . '/' . $className . '.php';
        if(file_exists($fname)) {
            include($fname);
        }
    }
    return(class_exists($className));
}