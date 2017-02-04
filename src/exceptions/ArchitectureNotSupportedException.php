<?php

/**
 * Created by PhpStorm.
 * User: gonher
 * Date: 03/02/17
 * Time: 21:41
 */
class ArchitectureNotSupportedException extends RuntimeException
{

    public function __construct($title = "Architecture provided is not supported", $message = '', $code = 'anse-0') {
        //todo show personalized error message
        //return parent::__construct($message, $code);
        //return __DIR__."/views/".$code.".php";
    }

}