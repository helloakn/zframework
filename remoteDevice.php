<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace zFramework;

class RemoteDevice{

  
    function __construct() {
    }

    static function Device(){
        $browser = get_browser(null, true);
        //$browser = get_browser();
       // echo "hello world\n";
       // print_r($browser);
       // echo "end hello world\n";
        return $browser?$browser['browser']:"unknown";
        return $browser['browser'];
    }
    static function ip(){
        return $_SERVER['REMOTE_ADDR'];
    }

}
?>