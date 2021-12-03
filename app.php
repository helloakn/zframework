<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace API\Application;
$providersDir = "providers/";
include 'schema/database.php';

include $providersDir.'env.php';
use zFramework\providers\Request;
use zFramework\Schema\Database;
use zFramework\providers\ExceptionHandler;
use zFramework\Auth;

//error_reporting(E_ERROR | E_PARSE);
class App {
    public $rootDir = "";
    public $routePrefix = [];
    public $guard = "";
    private $prefixIndex = -1;
    public $db = null;
    function __construct() {
        $this->routeList = [];
        $this->rootDir = getCwd()."/../";
        $this->controllerDir = getcwd().'/../controller/';
        //$this->db = new Database();
        //echo "app ";
        $this->db = Database::Instance();
        //echo " end-app ";

    }
    
    function replace($str,$replaceList){
        foreach($replaceList as $val){
            $str = str_replace($val,"",$str);
        }
        return $str;
    }

    function start(){
        $url = $_SERVER['REQUEST_URI'];
        $url = explode("?",$url);
        $url = $url[0];
        $replaceList = array(
            "/index.php/"
        );
        $url = $this->replace($url,$replaceList);
        $this->route($url);
    }

    //function addroute($method,$url,$controllerPath,$functionName){
    function addroute(){
        //$method,$url,$controllerPath,$functionName
        $method = null;
        $url= null;
        $controllerPath= null;
        $functionName= null;
       
        $numArgs = func_num_args();
        $args = func_get_args();
        if($numArgs==3){
            $method = $args[0];
            $url= $args[1];
            $controllerPath= null;
            $functionName=$args[2];
        }
        else{
            $method = $args[0];
            $url= $args[1];
            $controllerPath= $args[2];
            $functionName=$args[3];
        }
        $url = implode("",$this->routePrefix).$url;
        $url = $url[0]=="/"?$url:"/".$url;
        $url = str_replace("//","/",$url);
        //echo "/".$url.'<br>';exit;
        $this->routeList[$url][$method] =array(
            'includeClass' => $controllerPath==""?false:true,
            'method' => $method,
            'controller' => $controllerPath,
            'function' => $functionName,
            'guard' => $this->guard
        );
    }
    
    function routeGuard($guard,$function){
        $this->guard = $guard;
        call_user_func($function,$this);
       // print_r($this->routePrefix);exit;
       // $this->routePrefix[$this->prefixIndex] = "";
        $this->guard = "";
        //print_r($this->routePrefix);exit;
    }
    
    function withGuard($guard){
        $this->guard = $guard;
        return $this;
    }
    function routePrefix($prefix,$function,$defaultPrefix=""){
        $this->prefixIndex = $this->prefixIndex  +1;
        /*
        $this->routePrefix = $this->routePrefix."/".$prefix;
        var_dump($function);exit;
        call_user_func($function,$this,);
        $this->guard = "";
        $this->routePrefix = "";
        */
           # echo "default -> ".$defaultPrefix."<br>";
         #   echo $prefix."<br>";
         #   echo $this->prefixIndex;
           // $this->routePrefix[$this->prefixIndex].$defaultPrefix."/".$prefix;
        $this->routePrefix[$this->prefixIndex] = $prefix[0]=="/"?$prefix:"/".$prefix;
        //var_dump($function);exit;
        //echo $defaultPrefix."1<br>";
        //echo $this->routePrefix."1<br>";
        call_user_func($function,$this,$prefix);
        //$this->guard = "";
        
        unset($this->routePrefix[$this->prefixIndex]);
        $this->prefixIndex = $this->prefixIndex -1;
    }

    function mapping(){

    }
    function goRoute($url,$method,$parameters){
        //print_r($url);
        //print_r($parameters);exit;
        $route = $this->routeList[$url][$method];
            if($route['guard']!=''){
                $status = Auth::guard($route['guard'])->isLogin();
                if(!$status){
                    $data = array(
                        "status"=>403,
                        "message" => "Access Denied for Incorrect Token"
                    );
                    
                    Response::outPut($data);
                    return false;
                }
            }

            
            if( $route['includeClass']==true){
                $nameSpace = "Controller\\".str_replace("/","\\",$route["controller"]);
                $classPath =  $this->controllerDir.$route["controller"].".php";
               
                if(file_exists($classPath)){
                    include $classPath;
    
                    $functionName = $route['function'];
                    $obj = new $nameSpace();
                    $request = new Request($this->db );
                    if(in_array($functionName,get_class_methods($obj)))
                    {
                        $result = $obj->$functionName($request,$parameters);
                        Response::outPut($result);
                    }
                    else{
                        throw new ExceptionHandler(ExceptionHandler::FunctionNotFound($obj,$functionName,$route));
                    }
                    
                }
                else{
                    echo "Controller not found -> $classPath";
                }
            }
            else{
                $function = $route['function'];
                call_user_func($function,$this);
            }
    }
    function route($url){
       //echo ">>".$url;#exit;
       // var_dump($this->routeList);exit;
        //$db = Database::Instance();
       
       // print_r($this->routeList);exit;
        $parameters = new \StdClass;
        $ttff = false;
        foreach ($this->routeList as $k=>$v) {
            //var_dump($k);exit;
            $original_urls = explode("/", $k);
            $request_urls = explode("/", $url);
            if(count($original_urls )==count($request_urls)){
            

                $tf = true;
                foreach($original_urls as $ok=>$ov){
                   
                    if($ok!=0){
                        $isParameter = strpos($original_urls[$ok],"{");
                        if ($isParameter === false) {
                            if($original_urls[$ok] !=  $request_urls[$ok]  ){
                                $tf = false;
                               
                            }
                        }
                        else{
                            $par = str_replace("{","",$original_urls[$ok]);
                            $par = str_replace("}","",$par);
                            //$parameters->$par = $request_urls[$ok];
                            //$this->db->conn->real_escape_string($arr)
                            $parameters->$par = $this->db->conn->real_escape_string( $request_urls[$ok]);
                           // echo $parameters->$par;
                        }
                    }
                    
                }
                //var_dump($tf);
                if($tf){
                   
                    $ttff = true;
                    $this->goRoute($k,strtolower($_SERVER['REQUEST_METHOD']),$parameters);
                }
                else{
                   // $ttff = false;
                   // throw new ExceptionHandler(ExceptionHandler::RouteNotFound($url,$this->routeList));
                }

            }
            

        }
        //exit;
//var_dump($ttff);
        if (!$ttff) {
           
            ///go rute
            
            throw new ExceptionHandler(ExceptionHandler::RouteNotFound($url,$this->routeList));
            
        }
    }
}

$app = new App();

include 'Main.php';
include $providersDir.'ExceptionHandler.php';

$route = $app;

include '../route/route.php';
$app = $route;

include 'remoteDevice.php';
include 'auth.php';
include 'schema/table.php';
include $providersDir.'request.php';
include $providersDir.'validation.php';
include $providersDir.'response.php';
include $providersDir.'uploader.php';
include $providersDir.'S3.php';
include $providersDir.'Notifier.php';
include $providersDir.'Html.php';
include 'hash.php';
//print_r(Auth::guard);

$dirs = scandir($app->rootDir."model");
foreach($dirs as $dir){
    if(strpos($dir, ".php") !== false){
        include $app->rootDir."model/".$dir;
    }
}

$dirs = scandir($app->rootDir."extenstions");
foreach($dirs as $dir){
    if(strpos($dir, ".php") !== false){
        include $app->rootDir."extenstions/".$dir;
    }
}


$app->start();
?>