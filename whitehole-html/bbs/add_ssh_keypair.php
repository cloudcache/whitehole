<? include_once("./_head.whitehole.php"); ?>

<?
include "../db_conn.php";
include "../functions.php";
?>

<html>
<head>
	<title>Whitehole - Add SSH-KeyPair</title>
</head>
<body>
<form name=add_ssh_keypair method=post action=proc-add_ssh_keypair.php>
<table align=center width=500 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Description
		<td align=center><input type=text name=description size=20 maxlength=20>
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
