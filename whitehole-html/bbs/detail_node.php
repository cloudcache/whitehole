<?
$ip_address=$_GET[ip_address];
$hostname=$_GET[hostname];

//echo $ip_address;
//exit;

$conn=libvirt_connect("qemu+ssh://root@$ip_address/system");

$node_info=libvirt_node_get_info($conn);
//print_r($node_info);

$hypervisor_info=libvirt_connect_get_hypervisor($conn);
//print_r($hypervisor_info);
//exit;

//$active_vm_count=libvirt_domain_get_counts($conn);
//print_r($active_vm_count);



$model = $node_info[model];
$memory = $node_info[memory]/1024;
$cpus = $node_info[cpus];
$nodes = $node_info[nodes];
$sockets = $node_info[sockets];
$cores = $node_info[cores];
$threads = $node_info[threads];
$mhz = $node_info[mhz];

$hv = $hypervisor_info[hypervisor];
$hv_ver_major = $hypervisor_info[major];
$hv_ver_minor = $hypervisor_info[minor];
$hv_release = $hypervisor_info[release];
$hv_full_string = $hypervisor_info[hypervisor_string];
$hv_full_string = $hypervisor_info[hypervisor_string];

//echo "model : ".$model."<br>";
//echo "memory: ".$memory."<br>";
//echo "cpus : ".$cpus."<br>";
//echo "nodes : ".$nodes."<br>";
//echo "sockets : ".$sockets."<br>";
//echo "cores : ".$cores."<br>";
//echo "threads : ".$threads."<br>";
//echo "mhz : ".$mhz."<br>";
?>

<html>
<head>
	<title>Whitehole : Status Node</title>
	<link rel="stylesheet" href="style01.css" type="text/css">
<!--
	<script language='javascript'> 
		window.setTimeout('window.location.reload()',60000);
	</script>
-->
</head>
<body>


<table width=300 border=1 cellspacing=0 cellpadding=0>
	<tr align=center>
		<td align=center>IP
		<td align=center><?=$ip_address?>
	<tr align=center>
		<td align=center>Hostname
		<td align=center><?=$hostname?>
	<tr align=center>
		<td align=center>Hypervisor
		<td align=center><?=$hv_full_string?>
	<tr align=center>
		<td align=center>Model
		<td align=center><?=$model?>
	<tr align=center>
		<td align=center>Memory
		<td align=center><?=$memory?> MB
	<tr align=center>
		<td align=center>CPU
		<td align=center><?=$cpus?> EA
	<tr align=center>
		<td align=center>Node
		<td align=center><?=$nodes?> EA
	<tr align=center>
		<td align=center>Socket
		<td align=center><?=$sockets?> EA
	<tr align=center>
		<td align=center>Cores
		<td align=center><?=$cores?> EA
	<tr align=center>
		<td align=center>threads
		<td align=center><?=$threads?> EA
	<tr align=center>
		<td align=center>Mhz
		<td align=center><?=$mhz?> Mhz
</table>
