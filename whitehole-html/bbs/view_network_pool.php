<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";
include "../check_admin.php";


$query="select * from network_pool";
#$query="select * from network_pool where used='1'";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
?>
<html>
<head>
	<title>Whitehole - Network Pool</title>
</head>
<body>

<table align=center width=500 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>IP Address
		<td align=center bgcolor=99CCFF><b>Used
		<td align=center bgcolor=99CCFF><b>VM UUID
		<td align=center bgcolor=99CCFF><b>Account
		<td align=center bgcolor=99CCFF><b>Reset
	</tr>

<?
while ($data=@mysql_fetch_row($result)) {
	$ip_address=$data['0'];
	$used=$data['1'];
	$vm=$data['2'];
	$account=$data['3'];
?>
	<tr align=center>
		<td><?=$ip_address?>
		<td><?=$used?>
		<td><?=$vm?>
		<td><?=$account?>
<?
		if ($used != "0") {
		 	echo("<td><a href=proc-reset_network_pool.php?ip_address=$ip_address><font color=red>Reset</font></a>");
		} else {
			echo("<td>");
		}
?>
	</tr>
<?
}
?>
</table>
</body>
</html>
<? include_once("./_tail.whitehole.php"); ?>
