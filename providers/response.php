<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace API\Application;
use API\Application\Database;
class Response{
    
    private $db = NULL;
    private static $_instance = null;

    function __construct() {
    }
    function isJson($string) {
        error_reporting(E_ERROR | E_PARSE);
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    static function outPut($result){
        self::$_instance = self::$_instance === null ? new self : self::$_instance;
        if(is_array($result)){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
        }
        else if(is_object($result)){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
        }
        else{
            //print_r($result);
            echo $result;
        }
        
    }
}
?>