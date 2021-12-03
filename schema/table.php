<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace zFramework\Schema;
use zFramework\Schema\Database;
class Table{
    protected $defaultTableName="";
    protected $defaultColumnName=[];
    protected static $autoIncreaseKeys = [];
    protected static $hiddenColumns = [];
    protected $staticPrimaryKey = [];
    protected $whereCase = "";
    protected $orderBy = "";
    protected $groupBy = "";
    protected $softDelete = false;
    protected $database = NULL;
    private static $_instance = null;
    protected $defaultPros = [
        "database",
        "hiddenColumns",
        "groupBy",
        "orderBy",
        "whereCase",
        "defaultTableName",
        "defaultColumnName",
        "autoIncreaseKeys",
        "primaryKeys",
        "staticPrimaryKey",
        "_instance",
        "_fields",
        "retrieve",
        "defaultPros",
        "softDelete",
        "tableName",
        "columnName"
    ];
    function __construct($db=NULL) {
       //$this->database = new Database();
       //echo "constructor";
        // // //$this->database = Database::Instance();
        //$this->defaultTableName = static::$tableName;
        //$this->staticPrimaryKey = static::$primaryKeys;
        if($db!==NULL){
            
            $this->database = $db;
        }
        else{
            //echo "-";
            //echo "Request ";
            $this->database = Database::Instance();
        }
    }
    public function __call($name, $arguments) {
        //echo "__call";
        /*
        self::$_instance = (self::$_instance === null ? new self : self::$_instance);
        self::$_instance->database = Database::Instance();
        $name = "_".$name;
        $functionList = get_class_methods($this);
        if(in_array($name,$functionList)){
            self::$_instance->defaultTableName =static::$tableName;
            self::$_instance->staticPrimaryKey = static::$primaryKeys;
            return self::$_instance->$name(
                self::$_instance->defaultTableName,
                self::$_instance->staticPrimaryKey,
                $arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
        */
        $name = "_".$name;
        $functionList = get_class_methods($this);
        if(in_array($name,$functionList)){
            //echo $this->defaultTableName;exit;
            if(!$this->defaultTableName){
                $this->defaultTableName =static::$tableName;
                $this->staticPrimaryKey = static::$primaryKeys;
            }
            return $this->$name(
                $this->defaultTableName,
                $this->staticPrimaryKey,
                $arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
    }
    public static function __callStatic($name, $arguments) {
        if(in_array($name,array('find','select'))){
            //echo "haha";
            self::$_instance = new self ;
        }
        else{
            self::$_instance = (self::$_instance === null ? new self : self::$_instance);
        }
        
        //echo "table";
        self::$_instance->database = Database::Instance();
        $name = "_".$name;
        $functionList = get_class_methods(self::$_instance);
        if(in_array($name,$functionList)){
            self::$_instance->defaultTableName =static::$tableName;
            self::$_instance->staticPrimaryKey = static::$primaryKeys;
           return self::$_instance->$name(self::$_instance->defaultTableName,
           self::$_instance->staticPrimaryKey,
           $arguments);
        }
        else{
            throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,$name));
        }
    }
    function _select($tableName,$primaryKeys,$args){
        $this->whereCase = "";
        $this->groupBy = "";
        $this->defaultTableName = $tableName;
        $this->defaultColumnName = [];
        foreach($args as $arg){
            $this->defaultColumnName[] = $arg;
        }
        return $this;
    }
    function where($queryString){
       // $this->whereCase = "";
        $this->whereCase = ($this->whereCase==""?" WHERE ".$queryString:$this->whereCase." AND ".$queryString);
        return $this;
    }
    function orderBy($queryString){
        $this->orderBy = " ORDER BY ".$queryString;
        return $this;
    }
    function groupBy($queryString){
        $this->groupBy = " GROUP BY ".$queryString;
        return $this;
    }
    function get(){
       $select = $this->defaultColumnName ? implode(",",$this->defaultColumnName) : " * ";
       $oBy = $this->orderBy!==""?$this->orderBy:"";
       $cmdString = "SELECT ".$select." FROM ".$this->defaultTableName.
       ($this->whereCase!==""?$this->whereCase:"").
       ($this->groupBy!==""?($this->groupBy.$oBy):$oBy);
       $database = $this->database;
       //echo $cmdString;exit;
        $result = $database->query($cmdString);  
        return $result->num_rows==0 ? false: $result;  
    }
    function getAll(){
        $select = $this->defaultColumnName ? implode(",",$this->defaultColumnName) : " * ";
        $oBy = $this->orderBy!==""?$this->orderBy:"";
        $cmdString = "SELECT ".$select." FROM ".$this->defaultTableName.
        ($this->whereCase!==""?$this->whereCase:"").
        ($this->groupBy!==""?($this->groupBy.$oBy):$oBy);
        $database = $this->database;
        //if($this->defaultTableName=="Branch"){
           // echo $cmdString;//exit;
       // }
        
         $result = $database->query($cmdString);  
        //return $result->num_rows==0 ? false: $result;  
        if($result->num_rows==0){
            return false;
        }
        else{
            $data = []; 
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        }

    }
    function paginate($page_at,$row_count){
        $select = $this->defaultColumnName ? implode(",",$this->defaultColumnName) : " * ";
        $oBy = $this->orderBy!==""?$this->orderBy:"";
        $cmdString = "SELECT ".$select." FROM ".$this->defaultTableName.
        ($this->whereCase!==""?$this->whereCase." AND deleted_at IS NULL":" WHERE deleted_at IS NULL").
        ($this->groupBy!==""?($this->groupBy.$oBy):$oBy);
        $database = $this->database;
        $paginatecmdString = $cmdString . " LIMIT $page_at,$row_count";
       // echo $cmdString;exit;
        
         $result = $database->query($cmdString);  
        //return $result->num_rows==0 ? false: $result;  
        if($result->num_rows==0){
            return false;
        }
        else{
            $totalRecords = $result->num_rows;
            $total_page = $totalRecords/ $row_count;
            
            $result = $database->query($cmdString); 

            $from = $page_at*$row_count;
            $paginatecmdString = $cmdString . " LIMIT $from,$row_count";
            //echo $paginatecmdString;

            $paginate = array(
                "page_at" => $page_at+1,
                "total_page" => ceil($total_page),
                "total_records_count" => $totalRecords
            );

            $result = $database->query($paginatecmdString); 

            $data = []; 
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $returnData = new \stdClass();
            $returnData->paginate = $paginate;
            $returnData->data = $data;
            return $returnData;
        }

    }
    function first(){
        $database = $this->database;
        $select = $this->defaultColumnName ? implode(",",$this->defaultColumnName) : " * ";
        $oBy = $this->orderBy!==""?$this->orderBy:"";
        $cmdString = "SELECT ".$select." FROM ".$this->defaultTableName.
        ($this->whereCase!==""?$this->whereCase:"").
        ($this->groupBy!==""?($this->groupBy.$oBy):$oBy);
        $cmdString = $cmdString." LIMIT 0,1";
        //echo $cmdString;
        //exit;
        $result = $database->query($cmdString);
        //var_dump($result);exit;
        if(!$result){
            return false;
        }
        if($result->num_rows==1){
            foreach($result as $key=>$value)
            {
                $obj = new \stdClass();
                foreach($value as $k=>$v)
                {
                    $obj->$k = $v;
                }
                
                return $obj;
            }
        }
        else{
            return false;
        }
        //return $database->query($cmdString);     
     }

     function retrieve(){
        $database = $this->database;
        $select = $this->defaultColumnName ? implode(",",$this->defaultColumnName) : " * ";
        $oBy = $this->orderBy!==""?$this->orderBy:"";
        $cmdString = "SELECT ".$select." FROM ".$this->defaultTableName.
        ($this->whereCase!==""?$this->whereCase:"").
        ($this->groupBy!==""?($this->groupBy.$oBy):$oBy);
        $cmdString = $cmdString." LIMIT 0,1";
        //echo $cmdString;
        //exit;
        $result = $database->query($cmdString);
        //var_dump($result);exit;
        if(!$result){
            return false;
        }
        if($result->num_rows==1){
            foreach($result as $key=>$value)
            {
                $record = new \stdClass();
                foreach($value as $k=>$v)
                {
                    $record->$k = $v;
                }
                $obj = $this;
                $obj->_fields = $record;
                return $this;
               // return $obj;
            }
        }
        else{
            return false;
        }
        //return $database->query($cmdString);     
    }

    function _delete($tableName,$primaryKeys,$args){
        $database  = $this->database;
        $cmdString = "UPDATE $tableName SET deleted_at=". "'".date("yy-m-d h:i:s")."', ".
        "updated_at='".date("yy-m-d h:i:s")."' WHERE ".$args[0];
        //echo $cmdString;
        $result = $database->query($cmdString);
        if ($result  === TRUE) {
            return true;
        } else {
            echo $database->conn->error;
            exit;
        }
    }
    function _update($tableName,$primaryKeys,$args){
        #echo "hello";exit;
       // print_r($primaryKeys);#exit;
        //print_r(static::$tableName);
        $cmdString = "UPDATE $tableName SET ";
        $cmd = [];
        foreach (get_object_vars($this) as $prop_name => $prop_value) {
            if(!in_array($prop_name,$this->defaultPros)){
                //echo " x ".$prop_name.":".$prop_value." y ";
                if($primaryKeys[0]==$prop_name){
                    //print_r($primaryKeys[0]);print_r($prop_value);
                    //$cmd[] = $prop_name."=".(is_int($prop_value)?$prop_value:($prop_value==null?"NULL":"'".$prop_value."'"))." ";
                    //if($prop_name!='_fields'){
                    $cmd[] = $prop_name."=".(int)$prop_value." ";
                    //}
                }
                else{
                    if($prop_name=="updated_at"){
                        $prop_value =date("yy-m-d h:i:s");
                    }
                   // if($prop_name!='_fields'){
                       
                        $cmd[] = "`".$prop_name."`=".(is_int($prop_value)?$prop_value:($prop_value==null?"NULL":"'".$prop_value."'"))." ";
                    //}
                    
                   /* if(is_a($this->$prop_name,'stdClass')){
                        echo 'x';
                    }
                    else{
                        $cmd[] = "`".$prop_name."`=".(is_int($prop_value)?$prop_value:($prop_value==null?"NULL":"'".$prop_value."'"))." ";
                    }
                    */
                    
                    
                }
            }
        };
       // print_r($cmd);exit;
        //print_r($primaryKeys);exit;
        $key = $primaryKeys[0];
        //var_dump($this);
        //echo $key;exit;
        //echo $this->$key;exit;
       // $cmdString = $cmdString. implode(",",$cmd) ." WHERE ".$primaryKeys[0]."=".(is_int($this->$key)?$this->$key:"'".$this->$key."'").";";
       $cmdString = $cmdString. implode(",",$cmd) ." WHERE ".$primaryKeys[0]."=".$this->$key.";";
        $cmdString = str_replace("\\", "\\"."\\", $cmdString);
       //  echo $cmdString;exit;
        $database  = $this->database;
        $result = $database->query($cmdString);
        if ($result  === TRUE) {
            //$this->id = $database->conn->insert_id;
        } else {
            echo $database->conn->error;
            exit;
        }
    }
    function _find($tableName,$primaryKeys,$args){
        //echo $tableName;exit;
        #print_r($primaryKeys);exit;
       # print_r($args);exit;
        $id = $args[0];
        #echo $id;exit;
        $columnId = $primaryKeys[0];
        #echo $columnId;exit;
        //echo static::$tableName;exit;
        $this->defaultTableName = $tableName;
        $cmdString = "SELECT * FROM ".$tableName ." WHERE $columnId=".(is_int($id)?$id:"'".$id."'"). " AND deleted_at IS NULL";
        //$database = new Database();
        #echo $cmdString ;exit;
        $database = self::$_instance->database;
        $result =  $database->query($cmdString);   
        //echo $result['num_rows'];exit; 
        $hasRecord = false;
        if($result){
            foreach($result as $row){
                $hasRecord = true;
                foreach($row as $key=>$value){
                    $hiddenColumns = static::$hiddenColumns;
                    if(!in_array($key,$hiddenColumns)){
                        if($key!="primaryKeys"){
                            self::$_instance->$key = $value;
                        }
                    }
                }
            }
        }
        else{
            return false;
        }
        return $hasRecord ? self::$_instance : false;
    }
    function _save($tableName,$primaryKeys){
        //echo $tableName;exit;
        //echo $tableName;exit;
        //print_r($tableName);
        $columns = [];
        $data = [];
        //var_dump(get_object_vars($this));
        foreach (get_object_vars($this) as $prop_name => $prop_value) {
            if(!in_array($prop_name,$this->defaultPros)){
                $columns[] = "`".$prop_name."`";
                if($prop_value===null){
                    $data[] = "NULL";
                }
                else{
                    $data[] = "'".$prop_value."'";
                }
            }
        }
        if($this->softDelete){
            if (!in_array("`created_at`",$columns))
            {
                $columns[] = "`created_at`";
                $data[] = "'".date("yy-m-d h:i:s")."'";
            }
            if (!in_array("`updated_at`",$columns))
            {
                $columns[] = "`updated_at`";
                $data[] = "'".date("yy-m-d h:i:s")."'";
            }
            if (!in_array("`id`",$columns))
            {
                if(count($primaryKeys)==0){
                    $columns[] = "`id`";
                    $data[] = "NULL";
                }
            }
        }
        //echo "hello";
        //$tableName = static::$tableName;
       // echo implode(",",$columns);exit;
        $cmdString = "INSERT INTO `$tableName` (".implode(",",$columns).") VALUES " ."(".implode(",",$data).");";
        $cmdString = str_replace("\\", "\\"."\\", $cmdString);
        //  echo $cmdString; exit;
        $database  = $this->database;
        $result = $database->query($cmdString);
        if ($result  === TRUE) {
            $this->id = $database->conn->insert_id;
        } else {
            echo $database->conn->error;
            exit;
        }
    }
}
?>
