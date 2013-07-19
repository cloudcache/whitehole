<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$query="select count(*) from network_pool";
$result=@mysql_query($query);
if(!$result) {
	Query_Error();
}

$data=mysql_fetch_row($result);
if($data['0']!=0) {
	alert_msg("Network Pool is already exist.........!!!!");
}
?>

<html>
<head>
	<title>Whitehole - Add Network Pool</title>
</head>
<body>
<form name=add_ssh_keypair method=post action=proc-add_network_pool.php>
<table align=center width=600 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Begin
		<td align=center><input type=text name=begin size=30 maxlength=60>
	<tr align=center>
		<td align=center bgcolor=99CCFF>End
		<td align=center><input type=text name=end size=30 maxlength=60>
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
