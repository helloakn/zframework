<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace zFramework\Schema;
use zFramework\providers\Env;
class Database{
    public $conn = null;
    private static $_instance = null;
    function __construct() {
        //echo "__construct";
        //echo “db_“;
        $this->connectDB();
    }
    function connectDB(){
       // echo "connectDB";
        
        $servername = Env::get('DB_SERVER');
        //echo Env::get("DB_SERVER");exit;
        $username = Env::get('DB_USER');
        $password = Env::get('DB_PASSWORD');
        $db =Env::get('DB_NAME');
        self::$_instance  = $this;
        self::$_instance->conn = new \mysqli($servername, $username, $password,$db);
        if (self::$_instance->conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
            exit();
        }
        else{
           // echo “conn success”;
        }
        
    }
    static function Instance(){
        //echo "Instance";
        
        self::$_instance = (self::$_instance === null ? new self : self::$_instance);
        //self::$_instance->connectDB();
        
        return self::$_instance;
    }
    static function query($cmdString,$isStoredProcedure=false){
       //echo “i”;
        return self::$_instance->conn->query($cmdString);
        if($isStoredProcedure){
            $this->connectDB();
        }
    }
    static function fetchAllQuery($cmdString){
        //echo “i”;
         $result =  self::$_instance->conn->query($cmdString);
         //return $cmdString;
         //return  $result;
         if($result){
            $data = []; 
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
         }
         else{
             return false;
         }
     }

    static function executeQueryPaginate($cmdString,$page_at,$row_count){
        //echo “i”;
        //$page_at -= 1;
        $result = self::$_instance->conn->query($cmdString);
       
        $totalRecords = $result->num_rows;
        $total_page = $totalRecords/ $row_count;
        
        $from = ($page_at*$row_count)-$row_count;
        
        
        $paginate = array(
            "page_at" => $page_at,
            "total_page" => ceil($total_page),
            "total_records_count" => $totalRecords
        );
//var_dump($row_count);//exit;
        $paginatecmdString = $cmdString . " LIMIT $from,$row_count";
       // echo $paginatecmdString;
        $result = self::$_instance->conn->query($paginatecmdString); 

        $data = []; 
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $returnData = new \stdClass();
        $returnData->paginate = $paginate;
        $returnData->data = $data;
        return $returnData;


         return self::$_instance->conn->query($cmdString);
         if($isStoredProcedure){
             $this->connectDB();
         }
     }

    function close(){
        //echo “i”;
        self::$_instance->conn->close();
        $servername = Env::get('DB_SERVER');
        $username = Env::get('DB_USER');
        $password = Env::get('DB_PASSWORD');
        $db =Env::get('DB_NAME');
        self::$_instance->conn = new \mysqli($servername, $username, $password,$db);
         // self::$_instance->conn->initialize();
     }
}
//$db = new Database();
?>