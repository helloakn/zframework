<?php
namespace API\Application\Main;
class InitProject{
    private $argv = [];
    private $cmd = "";
    function __construct($args) {
        $this->$args = $args;
        if(count($this->$args)>=2){
            $this->cmd = $this->$args[1];
            //echo $this->cmd;
            //echo dirname(__FILE__);exit;
            $this->custom_copy(dirname(__FILE__)."/template",'./');
        }
        else{
            return false;
        }
        /*
        foreach($this->$args as $arg){
            $cmd = explode(":",$arg);
            
            if(count($cmd)>1){
                if($cmd[0]=="doc"){
                    $doc = $cmd[1];
                    $this->custom_copy(dirname(__FILE__)."/template",$doc);
                }
            }
        }
        */
    }
    function custom_copy($src, $dst) {  
  
        // open the source directory 
        $dir = opendir($src);  
      
        // Make the destination directory if not exist 
        $this->createDir($dst);  
      
        // Loop through the files in source directory 
        while( $file = readdir($dir) ) {  
      
            if (( $file != '.' ) && ( $file != '..' )) {  
                if ( is_dir($src . '/' . $file) )  
                {  
                    // Recursively calling custom copy function 
                    // for sub directory  
                    $this->custom_copy($src . '/' . $file, $dst . '/' . $file);  
                }  
                else {  
                    copy($src . '/' . $file, $dst . '/' . $file);  
                }  
            }  
        }  
        closedir($dir); 
    }
    private function createDir($dir){
       // echo $dir;exit;
       //var_dump(is_dir($dir));exit;
        if(!is_dir($dir)){
            //echo "created";
            mkdir($dir);
        }
    }

    public function initEnv($file){
        $file = fopen($file, "w") or die("Unable to open file!");
        fwrite($file, "DB_SERVER=localhost\n");
        fwrite($file, "DB_NAME=zote\n");
        fwrite($file, "DB_USER=root\n");
        fwrite($file, "DB_PASSWORD=\n");
        fclose($file);
    }
}
?>