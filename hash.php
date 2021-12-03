<?php
/*
Developed by : Akn via Zote Innovation
Date : 26-Oct-2020
Last Modify Date : 26-Oct-2020
*/
namespace zFramework;

class Hash{

    private static $_instance = null;
    private static $key = "!@#$%^&*()_+asdfghjkl;qwertyuiop[zxcvbnm,./1234567890";

    function __construct() {
        
    }
    
    static function generateHash($str){
        //$key = self::$key;
        //echo $key;exit;
        return md5($str);
        //$encoded = base64_encode(\mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(self::$key), $str, MCRYPT_MODE_CBC, md5(md5($key))));
        //echo $encoded;exit;
    }
    static function decodeHash($str){
        $key = self::$key;
        $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encoded), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    }
    static function randomString($length = 200)
    {
        $characters = '!@#$%^&*()_+-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return base64_encode($randomString);
    }

}
?>