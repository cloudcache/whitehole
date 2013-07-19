<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$node=$_GET['node'];
$name=$_GET['name'];
$uuid=$_GET['uuid'];
$order=$_GET['order'];

//echo $node;
//echo $name;
//echo $uuid;

$res=libvirt_connect("qemu+ssh://root@$node/system","0");
$dom=libvirt_domain_lookup_by_name($res,"$name");

libvirt_domain_create($dom);

sleep(2);
			
$xml=libvirt_domain_get_xml_desc($dom,"");
$json=json_encode(new SimpleXMLElement($xml));
$xml_array=json_decode($json,TRUE);
$vnc_port=@$xml_array['devices']['graphics']['@attributes']['port'];

$query="update info_vm set vnc='$vnc_port',status='1' where uuid='$uuid'";
$result_create=@mysql_query($query);
if (!$result_create) {
	Query_Error();
} else {
	run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php");
?>
<script language="javascript">
	location.href="view_vm.php?order=<?=$order?>&pre_create=yes&pre_vm_uuid=<?=$uuid?>&pre_vm_name=<?=$name?>&pre_target_node=<?=$node?>&pre_vnc_port=<?=$vnc_port?>"
</script>
<?
}
?>
