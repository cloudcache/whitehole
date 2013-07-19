<?

include "/var/www/html/db_conn.php";
include "/var/www/html/functions.php";

//$ssh_host = "192.168.100.122";

$query="select ip_address from info_node";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

while ($data=@mysql_fetch_row($result)) {
//	print_r($data);
	$ssh_host=$data['0'];
	$collect=rtrim(run_ssh_key($ssh_host,'root',"(date +%s; mpstat | awk '/all/ {print \$11}'; free -m | awk '/buffers\/cache/ {print \$NF}') | tr '\n' ' '"));
	#$collect=rtrim(run_ssh_key($ssh_host,'root',"(date +%s; mpstat | awk '/all/ {print \$11}'; xentop -b -i 1 | awk '/Mem:/ {print int(\$6/1024)}') | tr '\n' ' '"));
	$return=explode(' ',$collect);
	$update_time=$return['0'];
	$free_sys_cpu=100-$return['1'];
	$free_sys_mem=$return['2'];
	$conn=libvirt_connect("qemu+ssh://root@$ssh_host/system");
	$vm_count=libvirt_domain_get_counts($conn);
#print_r($vm_count);
	$total_vms=$vm_count['total'];
	$active_vms=$vm_count['active'];
	$inactive_vms=$vm_count['inactive'];
#echo $total_vms."\n";
#echo $active_vms."\n";
#echo $inactive_vms."\n";
#exit;
//	echo ($update_time."\n");
//	echo ($free_sys_cpu."\n");
//	echo ($free_sys_mem);

	$query="update info_node set update_time='$update_time',free_sys_cpu='$free_sys_cpu',free_sys_mem='$free_sys_mem',total_vm_count='$total_vms',active_vm_count='$active_vms',inactive_vm_count='$inactive_vms' where ip_address='$ssh_host'";
//	echo $query;

	$result_insert=@mysql_query($query);
	if (!$result_insert) {
		Query_Error();
	} 
}

?>
