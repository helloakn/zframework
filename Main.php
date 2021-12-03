<?php
/*
Developed by : Akn via Zote Innovation
Date : 28-Oct-2020
Last Modify Date : 28-Oct-2020
*/
namespace Zote\Application;

class Main{
    protected static $_instance = null;

    public function __call($name, $arguments) {
        
        $name = "_".$name;
        $functionList = get_class_methods($this);
        //print_r($functionList);
        if(in_array($name,$functionList)){
            return $this->$name($arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
    }
    public static function __callStatic($name, $arguments) {
        //print_r(static::$_instance );exit;
        return static::$_instance->$name($arguments);
        /*
        if(in_array($name,$functionList)){
           return self::$_instance->$name($arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound(self::$_instance,$name));
        }*/
    }
}

?>