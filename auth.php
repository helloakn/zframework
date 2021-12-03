<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace zFramework;
use zFramework\Schema\Database;
use zFramework\RemoteDevice;
use Model\Token;
use Model\Device;

class Auth{

    const DRAFT = 'draft';
    const PUBLISHED = 'published';
    protected $conn = NULL;
    protected $database = NULL;
    protected $defaultGuard = "user";
    protected $token = "";
    public $authUser = NULL;

    /*protected $guards = [
        'default' => 'User',
        'User' =>[
            'userTable' => 'User',
            'userFields' => ["id","email",'display_name'],
            'tokenTable' => 'token:user_id',
        ],
        'staf' =>[
            'userTable' => 'User',
            'userFields' => ["id","email",'display_name'],
            'tokenTable' => 'token:user_id',
        ],
    ];*/
    protected $guards = []; 

    private static $_instance = null;

    function __construct() {
        $this->database =  Database::Instance();
        $this->guards =  require '../provider/authorization.php';
    }
    static function guard($usr){
        self::$_instance = (self::$_instance === null ? new self : self::$_instance);
        self::$_instance->defaultGuard = $usr;
        return self::$_instance;
    }

    function login($loginInfo){
        $cmdString = "";
        /*
        $id = (array_key_exists("email",$loginInfo) ? "email='". $loginInfo['email']."'" :
                (array_key_exists("phone",$loginInfo) ? "phone='".$loginInfo['phone']."'"  : 
                    (array_key_exists("username",$loginInfo) ? "username='".$loginInfo['username']."'"  : NULL)));
        */
        foreach($loginInfo as $k=>$v){
            $query = $k."='".$v."'";
            $cmdString .= $cmdString=="" ? $query : " AND " . $query ;
        }
        $cmdString = " SELECT * FROM ". $this->guards[$this->defaultGuard]['userTable'] ." WHERE ".$cmdString." LIMIT 0,1";
      //  echo $cmdString; exit;
        $result = $this->database->query($cmdString);
        //ternary operator 
        //return $result->num_rows==1?true:false;
        switch($result->num_rows){
            case 0:
                return false;
            break;
            default:
            $authUsr = $this->generateAuthUser($result);
            $device = $this->generateDevice();
            $token = $this->generateToken($authUsr,$device);
            return true;
            break;
        }

    } 

    private function generateRandomString($length = 200)
    {
        $characters = '!@#$%^&*()_+-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generateDevice(){
       // echo "generate dev";
        $ip = RemoteDevice::ip();
        $deviceName = RemoteDevice::Device();

        $query = "device_info='".$this->database->conn->real_escape_string($deviceName)."' AND ip='".$this->database->conn->real_escape_string($ip)."'";
        //print_r($query);exit;
      //  print_r($query);
        $device = Device::select("*")->where($query)->first();
        //print_r($device->id);exit;
        if($device){
           // echo "found dev";
            return $device;
        }
        else{
          //  echo "not found dev";
            $device = new Device();
            $device->ip = $ip;
            $device->device_info = $deviceName;
          //  print_r($device);exit;
            $device->save();
        }
       // echo "end ";
        return $device;
    }

    function generateToken($usr,$device){
        //echo $this->defaultGuard;
       // print_r($this->guards[$this->defaultGuard]);exit;
        $vToken = $this->generateRandomString(100);
        $tmp = explode(":",$this->guards[$this->defaultGuard]['tokenTable']);
        $tokenTable = $this->guards[$this->defaultGuard]['tokenModel'];
        $tokenID = $tmp[1];
        //echo $tokenID;exit;
        //$token = new $this->guards[$this->defaultGuard]['tokenTable'][0]();
        $token = new $tokenTable();
        $token->device_id = $device->id;
        $token->$tokenID = $usr->id;
        $token->token = $vToken;
        $token->save();
        $this->token = $vToken;
        return $vToken;
    }

    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return false;
    }
    function isLogin(){
        $this->token = $this->getBearerToken();
        $guards = $this->guards[$this->defaultGuard];
        $userTable = $this->defaultGuard;//$guards['userTable']; //User
        $tokenTable = explode(":",$guards['tokenTable']); //Token:user_id
        $userFields = implode(",", $guards['userFields']);
        if($this->token){
            //$cmdString = "SELECT ".$userTable.".* FROM ".$tokenTable[0].",".$userTable." WHERE ".$tokenTable[0].".".$tokenTable[1]."=".$userTable.".id and ".$tokenTable[0].".token='".$this->token."'";
            $cmdString = "SELECT ".$userFields." FROM ".$userTable." WHERE id in(SELECT ".$tokenTable[0].".".$tokenTable[1]." FROM ".$tokenTable[0]." WHERE ".$tokenTable[0].".token='".$this->token."' AND deleted_at IS NULL)";
           // echo $this->defaultGuard;exit;
           // echo $cmdString;exit;
            $result =  $this->database->query($cmdString);
            $this->generateAuthUser($result);
            return $result->num_rows >= 1 ? true : false ;
        }
        else{
            return false;
        }
    }
    private function generateAuthUser($result ){
        $authUser = new \stdClass();
        foreach($result as $k=>$v){
            foreach($v as $key=>$value){
                
                $authUser->$key = $value;
            }
            $this->authUser = $authUser;
        }
        return $authUser;
    }
    function authUser(){
        
        $guards = $this->guards[$this->guards['default']];
        $userTable = $guards['userTable']; //User
        $tokenTable = explode(":",$guards['tokenTable']); //Token:user_id
        $userFields = implode(",", $guards['userFields']);
        //echo $userFields;
        //return $userFields;
        if($this->token){
            $cmdString = "SELECT ".$userFields." FROM ".$userTable." WHERE id in(SELECT ".$tokenTable[0].".".$tokenTable[1]." FROM ".$tokenTable[0]." WHERE ".$tokenTable[0].".token='".$this->token."')";
            //echo $cmdString;
            $result =  $this->database->query($cmdString);
            $authUser = new \stdClass();
            if($result->num_rows >= 1){
                return $this->generateAuthUser($result);
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    function getToken(){
        
        
        if($this->token){
            return $this->token;
        }
        else{
            return false;
        }
    }
}
?>