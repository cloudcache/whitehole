<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";
?>
<html>
<head>
	<title>Whitehole - Add VM Template</title>
</head>
<body>
<form name=add_template method=post action=proc-add_template.php>
<table align=center width=600 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Name
		<td align=center><input type=text name=name size=50 maxlength=100>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Description
		<td align=center><input type=text name=description size=50 maxlength=100>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Public
		<td align=center><input type=checkbox name=chk_public value=1 checked>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Featured
		<td align=center><input type=checkbox name=chk_featured value=1 checked>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Hypervisor
		<td align=center><select name=chk_hypervisor>
			<option>kvm</option>
			<!-- <option>xen</option> -->
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Bits
		<td align=center><select name=chk_bits>
			<option>64</option>
			<option>32</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>OS Type
		<td align=center><select name=chk_os_type>
			<option>RedHat</option>
			<option>Debian</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>URL
		<td align=center><input type=text name=url size=50 maxlength=200>
	<tr align=center>
		<td align=center bgcolor=99CCFF checked><b>Bootable
		<td align=center><input type=checkbox name=chk_bootable value=1 checked>
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
