<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$iso_name=$_GET['iso_name'];
$iso_node=$_GET['iso_node'];
$iso_vnc_port=$_GET['iso_vnc_port'];
$iso_uuid=$_GET['iso_uuid'];
$iso_ip=$_GET['iso_ip'];
$iso_gateway=$_GET['iso_gateway'];
$iso_netmask=$_GET['iso_netmask'];
$iso_broadcast=$_GET['iso_broadcast'];
$iso_network=$_GET['iso_network'];
?>
<html>
<head>
	<title>Whitehole - Node List</title>
<!--
	<link rel="stylesheet" href="style01.css" type="text/css">
	<script language='javascript'> 
		window.setTimeout('window.location.reload()',60000);
	</script>
-->
</head>
</body>

<table width=600 width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr>
		<td bgcolor=00ff00><b><center>VM Name
		<td><center><font color=blue><b><?=$iso_name?><b></font> (UUID: <?=$iso_uuid?>)
	<tr>
		<td bgcolor=ffff00><b><center>Network Info<br><font color=red>(메모 해두세요.)</font>
		<td>
			<b>IP Address:</b> <?=$iso_ip?><br>			
			<b>Gateway Address:</b> <?=$iso_gateway?><br>			
			<b>Netmask Address:</b> <?=$iso_netmask?><br>			
			<b>DNS Address:</b> <?=$dns_server?><br>			
			<b>Broadcast Address:</b> <?=$iso_broadcast?><br>			
			<b>Network Address:</b> <?=$iso_network?><br>			
			<b>HTTP Proxy Address (Optional):</b> http://<?=$dns_server?>:3128<br>			
</table>
<p>

<iframe src="vncviewer.php?name=<?=$iso_name?>&node=<?=$iso_node?>&vnc_port=<?=$iso_vnc_port?>&uuid=<?=$iso_uuid?>" name="OS Install" width="1250" height="1050" scrolling="auto" align="center"></iframe>

</body>
</html>
<? include_once("./_tail.whitehole.php"); ?>
