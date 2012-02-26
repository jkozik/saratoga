<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
header("Expires: Mon,26 JUL 1997 05:00:00 GMT");

if ($_GET["I"]=="DEMO" ){
   if ($_GET["F"] != ""){ 
      $fp=fopen("../Data/wflash.txt", "w");
      $TempStr=$_GET["F"];
      $TempStr2=str_replace(" ","+",$TempStr);
      fwrite($fp, "F=".$TempStr2);
   }else{
      $fp=fopen("../Data/wflash2.txt", "w");
      $TempStr=$_GET["S"];
      $TempStr2=str_replace(" ","+",$TempStr);   
      fwrite($fp, "S=".$TempStr2);
   }
   fclose($fp);
}
?>