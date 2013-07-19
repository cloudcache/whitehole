<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

///////////////

$create_time=time();
$rule_name=$_POST['rule_name'];
$description=$_POST['description'];
$uuid=rtrim(shell_exec("uuidgen"));


if (!$rule_name) {
	alert_msg("[Error] \"Rule Name\"을 정확히 지정하세요.");
	echo ("
		<script language=\"javascript\">
			location.href=\"view_security_group.php\"
		</script>
	");
	exit;
}

$query="insert into security_group values ('$uuid','$rule_name','$loguser','$description','0','$create_time')";
$result=@mysql_query($query);

if (!$result) {
    Query_Error();
}

$query="insert into security_ruleset values ('','$create_time','$uuid','$rule_name','$loguser','tcp','0.0.0.0','0','22','22','accept')";
$result=@mysql_query($query);

if (!$result) {
    Query_Error();
}


echo ("
	<script language=\"javascript\">
		location.href=\"view_security_group.php\"
	</script>
");
?>
