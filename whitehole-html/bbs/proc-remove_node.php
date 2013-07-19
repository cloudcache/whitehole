<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$total_vms=$_GET['total_vms'];
$ip_address=$_GET['ip_address'];

$path_pri="/home/mnt/pri";
$path_sec="/home/mnt/sec";

$query="select uuid from primary_storage";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
$data=@mysql_fetch_row($result);
$pri_uuid=$data['0'];

$query="select uuid from secondary_storage";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
$data=@mysql_fetch_row($result);
$sec_uuid=$data['0'];

$data=@mysql_fetch_row($result);

$error_message="Can't Remove!!! Because... This Secondary Storage is mounted.....";
if ($total_vms > 0) {
	alert_msg($error_message);
#	exit;
} else {
	run_ssh_key($ssh_host,'root',"sed -i '/\/sbin\/iptables -t nat -A POSTROUTING/d' /etc/rc.local");
	$conn=libvirt_connect("qemu+ssh://root@$ip_address/system","0");
	$pri_resource=libvirt_storagepool_lookup_by_uuid_string($conn,$pri_uuid);
	libvirt_storagepool_destroy($pri_resource);
	libvirt_storagepool_undefine($pri_resource);

	run_ssh_key($ssh_host,'root',"sed -i '/defaults,_netdev 0 0/d' /etc/fstab");
	run_ssh_key($ssh_host,'root',"/bin/umount $path_pri");

	$sec_resource=libvirt_storagepool_lookup_by_uuid_string($conn,$sec_uuid);
	libvirt_storagepool_destroy($sec_resource);
	libvirt_storagepool_undefine($sec_resource);

	run_ssh_key($ssh_host,'root',"sed -i '/defaults,_netdev 0 0/d' /etc/fstab");
	run_ssh_key($ssh_host,'root',"/bin/umount $path_sec");

	$query="delete from info_node where ip_address='$ip_address'";
	$result=@mysql_query($query);
	if (!$result) {
		Query_Error();
	}
	echo ("
		<script language=\"javascript\">
			location.href=\"view_nodes.php\"
		</script>
	");
}

?>
