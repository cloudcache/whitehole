<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

//$ssh_host = "192.168.100.122";

$query="select count(*) from primary_storage";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

$data=mysql_fetch_row($result);
$chk_count=$data[0];

//echo $chk_count;
//exit;
if ($chk_count != 0) {
	alert_msg("Already Registered Primary Storage!!!");
	exit;
}

?>
<html>
<head>
	<title>Whitehole - Add Primary Storage</title>
</head>
<body>
<form name=add_new_node method=post action=proc-add_primary_storage.php>
<table align=center width=600 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Host
		<td align=center><input type=text name=host size=30 maxlength=50>
	<tr align=center>
		<td align=center bgcolor=99CCFF>FS Type
		<td align=center><select name=fs_type>
			<option value=nfs>NFS
			<option value=glusterfs>GlusterFS
	<tr align=center>
		<td align=center bgcolor=99CCFF>Export Path
		<td align=center><input type=text name=export_path size=30 maxlength=50>
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
