<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$order = $_GET['order'];
$vm_ip = $_POST['vm_ip'];
$vm_name = $_POST['vm_name'];
$vm_uuid = $_POST['vm_uuid'];
$dst_host_ip = $_POST['dst_host_ip'];
$src_host_ip = $_POST['src_host_ip'];

$path_nwfilter_xml="/home/mnt/sec/xml-nwfilter";

#echo $vm_ip;
#echo $vm_name;
#echo $dst_host_ip;
#exit;

$src_conn=libvirt_connect("qemu+ssh://root@$src_host_ip/system","0");
$dst_conn=libvirt_connect("qemu+ssh://root@$dst_host_ip/system","0");

$res=libvirt_domain_lookup_by_name($src_conn, $vm_name);

# nwfilter 등록 (DST)
run_ssh_key($dst_host_ip,'root',"virsh nwfilter-define $path_nwfilter_xml/vm-$vm_uuid.xml");

$result=libvirt_domain_migrate($res, $dst_conn, VIR_MIGRATE_LIVE | VIR_MIGRATE_PEER2PEER | VIR_MIGRATE_PERSIST_DEST | VIR_MIGRATE_UNDEFINE_SOURCE);

if ($result==false) {
	run_ssh_key($dst_host_ip,'root',"virsh nwfilter-undefine vm-$vm_uuid");
        alert_msg("Libvirt last error: ".libvirt_get_last_error());
        exit;
}

# nwfilter 삭제 (SRC)
run_ssh_key($src_host_ip,'root',"virsh nwfilter-undefine vm-$vm_uuid");

$query="update info_vm set node='$dst_host_ip' where name='$vm_name' and ip_address='$vm_ip'";
$result=@mysql_query($query);
if(!$result) {
	Query_Error();
}

$res_vnc=libvirt_domain_lookup_by_name($dst_conn, $vm_name);
$xml_vnc=libvirt_domain_get_xml_desc($res_vnc,"");
$json=json_encode(new SimpleXMLElement($xml_vnc));
$xml_array=json_decode($json,TRUE);
$vnc_port=@$xml_array['devices']['graphics']['@attributes']['port'];

$query="update info_vm set node='$dst_host_ip',vnc='$vnc_port' where name='$vm_name' and ip_address='$vm_ip'";
$result=@mysql_query($query);
if(!$result) {
	Query_Error();
}

#		window.opener.document.location.href = window.opener.document.URL
run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php");
echo ("
	<script language=\"javascript\">
		opener.parent.location=\"view_vm.php?order=$order\"
		window.close();
	</script>
");
?>
