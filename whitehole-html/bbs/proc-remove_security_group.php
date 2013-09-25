<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$rule_name=$_GET['rule_name'];
$uuid=$_GET['uuid'];
$vm_uuid=$_GET['vm_uuid'];
$account=$_GET['account'];

$query="select used_count from security_group where account='$account'";

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

$data=mysql_fetch_row($result);
$num_count=$data['0'];

if ($num_count!=0) {
    alert_msg("[Error] 지정된 Security Group 이 VM에 사용중입니다. 확인후 다시 시도 하세요.");
    exit;
}

$query="delete from security_group where uuid='$uuid'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}

$query="delete from security_ruleset where uuid='$uuid'";
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
