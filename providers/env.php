<?php
/*
Developed by : Akn via Zote Innovation
Date : 28-Oct-2020
Last Modify Date : 28-Oct-2020
*/
namespace zFramework\providers;
use zFramework\providers\ExceptionHandler;
class Env{
    private static $_instance = null;
    
    function _get($args){
        $_envFile = "../.env";
        $mode = "";
        if(file_exists($_envFile)){
            $file = fopen($_envFile,"r");
            $envData = [];
            while(! feof($file))
            {
                $content = explode("=",fgets($file));
                if(count($content)==2){
                    if($content[0]=="MODE"){
                        $mode = preg_replace("/\r|\n/", "", $content[1]);
                    }
                    else{
                        $envData[$content[0]] = preg_replace("/\r|\n/", "", $content[1]);
                    }
                
                }
            }
            fclose($file);
            return count($args)==1?(array_key_exists($mode.".".$args[0],$envData)==true?$envData[$mode.".".$args[0]]:NULL):NULL; 
        }
        else{
            return "";
        }

               
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