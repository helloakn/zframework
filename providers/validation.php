<?php
/*
Developed by : Akn via Zote Innovation
Date : 28-Oct-2020
Last Modify Date : 28-Oct-2020
*/
namespace zFramework\providers;
use zFramework\providers\ExceptionHandler;
use zFramework\providers\Request;
class Validation{

}
class Validator{
    private static $_instance = null;

    private $_isValidate = true;
    private $_error = []; 
    private $_key = "";

    function _max($args){

        if(isset($this->_keys)){
            $values = Request::get($this->_keys);
            if(is_array($values)){
                foreach($values as $i=>$v){
                    if($args[0]<strlen($v)){
                        $this->_isValidate = false;
                        $this->_error[$this->_keys][] = count($args)==2 ? $args[1] : $this->_keys." must be under ".$args[0];
                    }
                }
            }
            
        }
        else{
            $value = Request::get($this->_key);
            if($args[0]<strlen($value)){
                $this->_isValidate = false;
                $this->_error[$this->_key][] = count($args)==2 ? $args[1] : $this->_key." must be under ".$args[0];
            }
        }


        
        return $this;  
    }
    function _notnull($args){
        
        if(isset($this->_keys)){
            $values = Request::get($this->_keys);
            //echo '===\n';
           // print_r($this->_keys);
           // echo '---\n';
           // print_r($values);//exit;
           if(is_array($values)){
                foreach($values as $i=>$v){
                    if($v==null || $v==""){
                        $this->_isValidate = false;
                        $this->_error[$this->_keys][$i] = count($args)==1 ? $args[0] : $this->_keys." [$i] should not be null";
                    }
                }
           }
            
            
        }
        else{
            $value = Request::get($this->_key);
            if($value==null || $value==""){
                $this->_isValidate = false;
                $this->_error[$this->_key][] = count($args)==1 ? $args[0] : $this->_key." should not be null";
            }
        }
        
        return $this;  
    }
    function _min($args){

        if(isset($this->_keys)){
            $values = Request::get($this->_keys);
            if(is_array($values)){
                foreach($values as $i=>$v){
                    if($args[0]>strlen($v)){
                        $this->_isValidate = false;
                        $this->_error[$this->_keys][] = count($args)==2 ? $args[1] : "Minimum length of ". $this->_keys." is ".$args[0];
                    }
                }
            }
            
        }
        else{
            $value = Request::get($this->_key);
            if($args[0]>strlen($value)){
                $this->_isValidate = false;
                $this->_error[$this->_key][] = count($args)==2 ? $args[1] : "Minimum length of ". $this->_key." is ".$args[0];
            }
        }
        
        return $this;  
    }

    function _minetype($args){
        //  print_r($args);exit;
          //print_r($this->_keyFile);exit;
         // print_r($args);exit;
         //print_r($this->_keyFile);exit;
         if(isset($this->_keyFile)){
            $value = Request::getfile($this->_keyFile);
            //print($value);exit; 
            //print_r($value['type']);exit;
            //print_r($args);exit;
            if($value){
                if(!in_array($value['type'],$args)){
                    //print_r($value['type']); echo "\n"; print_r($args);
                    //echo "failed type";
                    $this->_isValidate = false;
                    $this->_error[$this->_keyFile][] = "not allow File Type";
                }
            }
            else{
                return $this;  
            }
        }
        else{
            $values = Request::getfile($this->_keyFiles);
            if($values){
                foreach($values['type'] as $i=>$v){
                    //print_r($values['type'][$i]);exit;
                  //  echo $values['type'][$i]; echo "xxx\n";
                    if(!in_array( $values['type'][$i],$args)){
                        $this->_isValidate = false;
                        $this->_error[$this->_keyFiles][] = "not allow File Type";
                    }
                }
                
            }
            else{
                return $this;  
            }
        }
        return $this;
         
    }

    function _extenstions($args){
      //  print_r($args);exit;
        //print_r($this->_keyFile);exit;
       // print_r($args);exit;
       //print_r($this->_keyFiles);exit;
       
       if(isset($this->_keyFile)){
            $value = Request::getfile($this->_keyFile);
            if($value){
                $file_parts = pathinfo($value['name']);
                if(array_key_exists('extenstion',$file_parts)){
                    if(!in_array($file_parts['extension'],$args)){
                        $this->_isValidate = false;
                        $this->_error[$this->_keyFile][] = "not allow File Extenstion";
                    }
                }
            }
            else{
                return $this;  
            }
        }
        else{
            $values = Request::getfile($this->_keyFiles);
            if($values){
                foreach($values['name'] as $i=>$v){
                   // print_r($values['name'][$i]);exit;
                   $file_parts = pathinfo($values['name'][$i]);
                    if(array_key_exists('extenstion',$file_parts)){
                        if(!in_array($file_parts['extension'],$args)){
                            $this->_isValidate = false;
                            $this->_error[$this->_keyFiles][] = "not allow File Extenstion";
                        }
                    }
                    
                }
                
            }
            else{
                return $this;  
            }
        }
        return $this;
       
    }

    function _file($args){
      // print_r($args);exit;
        $this->_keyFile = $args[0];
        //print_r($this->_keyFile);exit; 
        return $this;

    }
    function _files($args){
        //print_r($args);exit;
         $this->_keyFiles = $args[0];
         //print_r($this->_keyFile);exit; 
         return $this;
 
     }
    function _field($args){
        $this->_key = $args[0];
        return $this;
    }

    function _fields($args){
        //print_r($args);
        $this->_keys = $args[0];
        return $this;
    }

    function _error($args){
        return $this->_error;
    }
    function _validate($args){
        return $this->_isValidate;
    }

    function _seterror($args){
        //print_r($this->_key);
        $this->_isValidate = false;
        if(isset($this->_keys)){
            $this->_error[$this->_keys][] = $args[0];
        }
        else if(isset($this->_keyFile)){
            $this->_error[$this->_keyFile][] = $args[0];
        }
        else{
            $this->_error[$this->_key][] = $args[0];
        }
        
           
    }
    function _custom($args){
        call_user_func($args[0],$this);
        return $this;
    }
    function _rule($args){
        
        call_user_func($args[0],$this);
        return $this;
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