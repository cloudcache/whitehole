<?
$db_host = "localhost";
$db_user = "root";
$db_pass = "mysql_root_pw";
$db_name = "whitehole";

$Connect_DB = mysql_connect($db_host,$db_user,$db_pass);
$select_db = mysql_select_db($db_name);
?>
