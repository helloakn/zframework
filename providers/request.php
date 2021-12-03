<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace zFramework\providers;
use zFramework\Schema\Database;
class Request{
    
    public $db = NULL;
    private static $_instance = null;
    function __construct($db=NULL) {
        //var_dump($db);
        if($db!==NULL){
            
            $this->db = $db;
        }
        else{
            //echo "-";
            //echo "Request ";
            $this->db = Database::Instance();
        }
        
    }
    private function getFilter($arr){
        if(!is_array($arr)){
            $arr = $this->db->conn->real_escape_string($arr);
        }
        else{
            foreach($arr as $k => $v){
                $arr[$k] = is_array($arr[$k]) ? $this->getFilter($arr[$k]) : $this->db->conn->real_escape_string($arr[$k]);
             }
        }
        return $arr;
    }
    function _getfile($args){
        //print_r($args);exit;
        $index = $args[0];
        //print_r($index);exit;
        $data =  array_key_exists($index,$_FILES)?
                    $_FILES[$index]:NULL;
       //print_r($data);
        return $data;
    }
    function _getfiles($args){
        //print_r($args);exit;
        $index = $args[0];
        //print_r($index);exit;
        $data =  array_key_exists($index,$_FILES)?
                    $_FILES[$index]:NULL;
    //    /print_r($data);exit;
        return $data;
    }
    function _get($args){
        $index = $args[0];
        $data =  array_key_exists($index,$_GET)?
                $this->getFilter($_GET[$index]):
                (array_key_exists($index,$_POST)?$this->getFilter($_POST[$index]):NULL);
        
        return $data;
    }
    //default functions
    public function __call($name, $arguments) {
        $name = "_".strtolower($name);
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
        self::$_instance->db = (self::$_instance->db === null ? new Database() : self::$_instance->db);
        $name = "_".strtolower($name);
        $functionList = get_class_methods(self::$_instance);
        if(in_array($name,$functionList)){
            return self::$_instance->$name($arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
    }
    //end default functions
}
?>