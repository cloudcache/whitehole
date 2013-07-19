<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$path_nwfilter_xml="/home/mnt/sec/xml-nwfilter";

$uuid=$_GET['uuid'];
$name=$_GET['name'];
$hypervisor=$_GET['hypervisor'];
$target_node=$_GET['node'];
$sshkey_uuid=$_GET['sshkey_uuid'];
$ip_address=$_GET['ip_address'];

$query_p="select protect from info_vm where uuid='$uuid'";
$result_p=@mysql_query($query_p);
if (!$result_p) {
	Query_Error();
}
$data_p=mysql_fetch_row($result_p);
$protect=$data_p['0'];
if ($protect=="1") {
	alert_msg("[Error] 해당 VM($name)은 삭제보호(Protection) 중입니다. 먼저 삭제보호를 OFF하세요.");
	exit;
}

# SSH Key 카운트 업데이트
$query_sshkey_count="select used_count from ssh_keypair where uuid='$sshkey_uuid'";
$result_sshkey_count=@mysql_query($query_sshkey_count);
if (!$result_sshkey_count) {
	Query_Error();
}
$data_sshkey_count=@mysql_fetch_row($result_sshkey_count);
$sshkey_count_old=(int)$data_sshkey_count['0'];
if($sshkey_count_old>0) {
	$sshkey_count_new=$sshkey_count_old-1;
} else {
	$sshkey_count_new=0;
}
$query_sshkey_count="update ssh_keypair set used_count='$sshkey_count_new' where uuid='$sshkey_uuid'";
$result_sshkey_count=@mysql_query($query_sshkey_count);
if (!$result_sshkey_count) {
	Query_Error();
}

# Security Group 카운트 업데이트
$query_security_group_count="select used_count from security_group where uuid='$security_group_uuid'";
$result_security_group_count=@mysql_query($query_security_group_count);
if (!$result_security_group_count) {
	Query_Error();
}
$data_security_group_count=@mysql_fetch_row($result_security_group_count);
$security_group_count_old=(int)$data_security_group_count['0'];
if($security_group_count_old>0) {
	$security_group_count_new=$security_group_count_old - 1;
} else {
	$security_group_count_new=0;
}
$query_security_group_count="update security_group set used_count='$security_group_count_new' where uuid='$security_group_uuid'";
$result_security_group_count=@mysql_query($query_security_group_count);
if (!$result_security_group_count) {
	Query_Error();
}

# IP 반환
$query_ip="update network_pool set used='0',vm='',account='' where vm='$uuid'";
$result_ip=@mysql_query($query_ip);
if (!$result_ip) {
	Query_Error();
}

# libvirt 연결 타입 결정
if("$hypervisor"=="xen") {
	$conn=libvirt_connect("xen+ssh://root@$target_node/","0");
} else {
	$conn=libvirt_connect("qemu+ssh://root@$target_node/system","0");
}

# 도메인 확인
$resource=libvirt_domain_lookup_by_uuid_string($conn, $uuid);

run_ssh_key($target_node,'root',"virsh undefine --snapshots-metadata $uuid");

# 도메인 destroy / undefine
#if(!libvirt_domain_destroy($resource)) {
#	bail('Domain destroy failed with error: '.libvirt_get_last_error());
#}
$return_destroy=libvirt_domain_destroy($resource);
#if($return_destroy!=1) {
#	alert_msg_close('Error : Failed to Destroy VM (by libvirt)'.libvirt_get_last_error());
##	exit;
#}
/*
$return_undefine=libvirt_domain_undefine($resource);
if($return_undefine!=1) {
	alert_msg_close("Error : Failed to Undefine VM (by libvirt)");
#	exit;
}
*/

# nwfilter 삭제
run_ssh_key($target_node,'root',"virsh nwfilter-undefine vm-$uuid");
run_ssh_key('localhost','root',"rm -f $path_nwfilter_xml/vm-$uuid.xml");

# Image 삭제
#run_ssh_key('localhost','root',"rm -f /home/mnt/pri/instances/$uuid");

// VLNA 브리지 삭제
#run_ssh_key($target_node,'root',"/etc/xen/scripts/network-bridge-vlan vlan=$loguser_id stop");

# MRTG 삭제
run_ssh_key('localhost','root',"rm -f /home/whitehole/mrtg.cfg/$uuid*; rm -f /var/log/mrtg/$uuid*; rm -rf /var/www/mrtg/$uuid");

# DB에서 VM삭제
$query="delete from info_vm where uuid='$uuid'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
} else {
	run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php; rm -f /home/mnt/pri/instances/$uuid*");
	$query="delete from snapshots where vm_uuid='$uuid'";
	$result=@mysql_query($query);
	if (!$result) {
		Query_Error();
	}
	# 노드 정보 업데이트
	run_ssh_key('localhost','root',"/home/whitehole/update-dns.sh delete $dns_server $name.test.org $ip_address");

	echo ("
		<script language=\"javascript\">
			location.href=\"view_vm.php\"
		</script>
	");
}

?>
