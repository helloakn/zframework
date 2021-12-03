<?php
/*
Developed by : Akn via Zote Innovation
Date : 28-Oct-2020
Last Modify Date : 28-Oct-2020
*/
namespace zFramework\providers;
use zFramework\providers\ExceptionHandler;
class Html{
    private static $_instance = null;
    
    function _load($args){
        $dir = "../html/";
        #include($dir.$args[0].".php"); 
        $f = $dir.$args[0];
        #html/login
        #$f.".php" => html/login.php
        
        include((file_exists($f.".php")?$f.".php":$f.".html.php"));
        
    }
    public function __call($name, $arguments) {
        $name = "_".$name;
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
        $name = "_".$name;
        $functionList = get_class_methods(self::$_instance);
        if(in_array($name,$functionList)){
           return self::$_instance->$name($arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
    }
}

//echo Env::get('DB_NAME');
?>