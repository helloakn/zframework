<?php
/*
Developed by : Akn via Zote Innovation
Date : 28-Oct-2020
Last Modify Date : 28-Oct-2020
*/
namespace zFramework\providers;
use zFramework\providers\ExceptionHandler;
use zFramework\providers\Request;

class Uploader{
    private static $_instance = null;
    private $file = null;
    public function _upload($arg){
        var_dump($arg);
        echo "i'm u;oadin";
    }
    public function __call($name, $arguments) {
        if($name=="custom"){
           // print_r($arguments);exit;
        }
        
        $name = "_".strtolower($name);
        //echo $name."<br>";
        //echo json_encode($arguments);
        $functionList = get_class_methods($this);
        if(in_array($name,$functionList)){
            return $this->$name($arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
    }

    public static function __callStatic($name, $arguments) {
        self::$_instance = (self::$_instance === null ? new self : self::$_instance);
        $name = "_".strtolower($name);
        $functionList = get_class_methods(self::$_instance);
        if(in_array($name,$functionList)){
            return self::$_instance->$name($arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
    }
}

?>