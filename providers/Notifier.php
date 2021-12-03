<?php
namespace zFramework\providers;
class Notifier
{
    private static $_instance = null;

    private static $_key = null;
    private static $_host = null;
    private static $_port = null;
    private static $_target = null;

    function _setConfig($args){
        self::$_host = $args[0];
        self::$_port = $args[1];
        self::$_key = $args[2];
        //return $this->key;
    }
    function _sendSMS($args){
        //return "hello";
        $host    = self::$_host;//"elb-search-notification-1f8d31b3f685159b.elb.ap-southeast-2.amazonaws.com";
        $port    = self::$_port;//12345;

        $data = array(
            "key" => "3adfasdfasdfasdasdfasdf24234234dfasdf32rawsdfasdfq32radfasdfasdf",
            "type" => "from-notifier",
            "target" =>"SGW", // SGW for sms, NGW for noti
            //"data" => $argv[2]
            "data" => array(
                "phNo" =>  $args[0],//$argv[3],//"+95975",
                "message" =>  $args[1] //message
            )
        );
        $message = base64_encode(json_encode($data));
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
        // connect to server
        $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");  
        // send string to server
        socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
        // close socket
        socket_close($socket);   
        return $data;    
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
?>