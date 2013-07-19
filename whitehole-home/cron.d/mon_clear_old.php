<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
$Connect_DB = @mysql_connect($db_host,$db_user,$db_pass);
@mysql_select_db($db_name);

function Query_Error() {
	$ErrorNumber = @mysql_errno();
	$ErrorString = @mysql_error();
	$ErrorNoString = "DB Error:" . $ErrorNumber . "-" . $ErrorString;
	alert_msg_close($ErrorNoString);
#	exit;
}

$max_days="30";

$now_time=time();
$delete_time=$now_time-(86400 * $max_days);

$query="delete from monitoring where timestamp<'$delete_time'";
$result=mysql_query($query,$Connect_DB);
if (!$result) {
	Query_Error();
}
?>
