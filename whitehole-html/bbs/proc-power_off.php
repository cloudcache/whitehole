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

libvirt_domain_destroy($dom);
#libvirt_domain_shutdown($dom);

			
$query="update info_vm set status='0', vnc='-1' where uuid='$uuid'";
$result_create=@mysql_query($query);
if (!$result_create) {
	Query_Error();
} else {
	run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php");
?>
<script language="javascript">
	location.href="view_vm.php?order=<?=$order?>"
</script>
<?
}
?>
