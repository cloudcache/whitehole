<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

// Primary 스토리지 체크
$query_pri="select count(*) from primary_storage";
$result_pri=@mysql_query($query_pri);
if (!$result_pri) {
	Query_Error();
}

// Secondary 스토리지 체크
$query_sec="select count(*) from secondary_storage";
$result_sec=@mysql_query($query_sec);
if (!$result_sec) {
	Query_Error();
}

$chk_pri=@mysql_fetch_row($result_pri);
$chk_sec=@mysql_fetch_row($result_sec);

if ($chk_pri['0']==0 || $chk_sec['0']==0) {
	alert_msg_close("Primary/Secondary in not registered...!!!!");
	exit;
}
?>

<html>
<head>
	<title>Whitehole - Add Node</title>
</head>
<body>
<form name=add_new_node method=post action=proc-add_node.php>
<table align=center width=600 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>IP Address
		<td align=center><input type=text name=ip_address size=30 maxlength=15>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>root's Password
		<td align=center><input type=text name=root_password size=30 maxlength=30>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Hypervisor
		<td align=center><select name=hypervisor>
			<option value=kvm>KVM</option>
			<option value=xen>XEN</option>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>
</body>
</html>
<? include_once("./_tail.whitehole.php"); ?>
