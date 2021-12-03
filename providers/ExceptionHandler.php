<?php
/*
Developed by : Akn via Zote Innovation
Date : 27-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace zFramework\providers;

class ExceptionHandler{

    private static $_instance = null;
   
    function __construct() {
    }
    static function FunctionNotFound($me,$name,$route){
        //throw new ExceptionHandler(ExceptionHandler::FunctionNotFound($this,"methodname"));
        echo "<div style='background-color:red'><b>Function Not Found Exception</b><br></div>";
        echo "<div style='background-color:pink'>".get_class($me).":".$name." <b>NOT FOUND</b></div>";
        $functionList = get_class_methods($me);
        foreach($route as $k=>$v){
            echo "<div style='background-color:silver'>".$k." : ".$v."</div>";
        }
        exit();
    }
    static function MethodNotFound($me,$name){
        //throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,"methodname"));
        echo "<div style='background-color:red'><b>Method Not Found Exception</b><br></div>";
        echo "<div style='background-color:pink'>".get_class($me).":".$name."</div>";
        $functionList = get_class_methods($me);
        foreach($functionList as $k=>$v){
            echo "<div style='background-color:silver'>".get_class($me).":".$v."</div>";
        }
        exit();
    }
    static function RouteNotFound($url,$routeList){
        //throw new ExceptionHandler(ExceptionHandler::MethodNotFound($this,"methodname"));
        echo "<div style='background-color:red'><b>Route Not Found Exception</b><br></div>";
        echo "<div style='background-color:pink'> not found ".$url." in route list</div>";
        echo "<div style='background-color:gray'> Route List :";
        foreach($routeList as $k=>$v){
            echo "<div style='background-color:silver'>[".$v['method']."]:".$k."</div>";
        }
        echo "</div>";
        exit();
    }
    
    

}
?>