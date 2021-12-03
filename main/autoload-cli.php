<?php
/*
Developed by : Akn via Zote Innovation
Date : 27-Oct-2020
Last Modify Date : 26-Oct-2020
*/
$baseDir = str_replace("main","",dirname(__FILE__));
//echo $baseDir;exit;
include $baseDir.'providers/env.php';
include $baseDir.'schema/database.php';
include $baseDir.'schema/table.php';
include $baseDir.'main/InitProject.php';
include $baseDir.'main/console.php';
?>