<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

?>
<html>
<head>
	<title>Whitehole - Add VM from Temoplate</title>
<!--	<link rel="stylesheet" href="style01.css" type="text/css"> -->
</head>
<body>
<form name=add_new_vm method=post action=proc-add_vm.php>
<table align=center width=500 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Hostname
		<td align=center><input type=text name=hostname size=35 maxlength=20><br><font color=red>선택사항: Hostname 규칙 준수필!!<br></font>(밑줄(_)과 같은 특수문자 사용시 에러)
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Template
		<td align=center>
			<select name=template_uuid>
<?

$query="select uuid,description,size_verify,hypervisor,bits,os_type from vm_template where public='1'";
#if ($loguser=="admin") {
#	#$query="select uuid,description from vm_template where description not like 'Template-Vyatta%' order by name desc";
#	$query="select uuid,description from vm_template order by name desc";
#} else {
#	$query="select uuid,description from vm_template where description not like 'Template-Vyatta%' order by name desc";
#}

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
while ($data=@mysql_fetch_row($result)) {
//print_r($data);
	$uuid=$data['0'];
	$description=$data['1'];
#	$size_verify=$data['2'];
#	$hypervisor=$data['3'];
#	$bits=$data['4'];
#	$os_type=$data['5'];
		echo("<option value=$uuid>$description</option>");
		#echo("<option value=$uuid/$description/$size_verify/$hypervisor/$bits/$os_type>$description</option>");
#		echo("<input type=hidden name=uuid value=$uuid>");
#		echo("<input type=hidden name=size_verify value=$size_verify>");
#		echo("<input type=hidden name=hypervisor value=$hypervisor>");
#		echo("<input type=hidden name=bits value=$bits>");
}
?>
	</tr>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Core
		<td align=center><select name=core>
			<option value=1><b>1 Core</option>
			<option value=2><b>2 Core</option>
			<option value=4><b>4 Core</option>
			<option value=8><b>8 Core</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Memory
		<td align=center><select name=memory>
			<option value=1024>1 GB</option>
			<option value=2048>2 GB</option>
			<option value=4096>4 GB</option>
			<option value=8192>8 GB</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Data-Volume
		<td align=center><select name=data_volume>
			<option value=0>None</option>
			<option value=100>100 GB</option>
			<option value=200>200 GB</option>
			<option value=500>500 GB</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>SSH KeyPair
		<td align=center><select name=ssh_keypair_uuid>
<?
//echo $loguser;
$query="select uuid,description from ssh_keypair where account='$loguser'";

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

while ($data=@mysql_fetch_row($result)) {
	$ssh_keypair_uuid=$data['0'];
	$description=$data['1'];
		echo ("<option value='$ssh_keypair_uuid'>$description</option>");
}
?>
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
