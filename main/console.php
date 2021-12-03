<?php
/*
Developed by : Akn via Zote Innovation
Date : 3-Nov-2020
Last Modify Date : 3-Nov-2020
*/
namespace API\Application\Main;
use API\Application\Main\InitProject;

class Engine{
    public $argv = [];
    
    private $command =  "";
    private $commandList = ["run"];
    private $color = array(
        "Default" => "\e[39m",
        "Black" => "\e[30m",
        "Red" => "\e[31m",
        "Green" => "\e[32m",
        "Yellow" => "\e[33m",
        "Blue" => "\e[34m",
        "Magenta" => "\e[35m",
        "Cyan" => "\e[36m",
        "LightGray" => "\e[37m",
        "DarkGray" => "\e[90m",
        "LightRed" => "\e[91m",
        "LightGreen" => "\e[92m",
        "LightYellow" => "\e[93m",
        "LightBlue" => "\e[94m",
        "LightMagenta" => "\e[95m",
        "LightCyan" => "\e[96m",
        "White" => "\e[97m",
    );

    function __construct($argv) {
        $this->argv = $argv;
        $argCount = count($this->argv);
        if($argCount>1){
            
        }
        else{
            echo "What u want to do? \n";
            echo $this->_echo("Yellow","You need argument\n");
            echo $this->_echo("LightCyan","php zote start?\n");
            exit();
        }
    }
    private function _echo($color,$text,$newLine='yes'){
        echo $this->color[$color].$text.($newLine=='no'?"":"\n");
        echo $this->color['White'];
    }
    
    function start(){
        #echo "starting"; 
        echo $this->_echo("Blue",$command);
        $command = $this->argv[1];
        switch($command){
            case 'init':
                
                $initPro = new InitProject($this->argv);
            break;
        }
    }
}

$engine = new Engine($argv);
$engine->start();
# php zote-framework/zote init
?>