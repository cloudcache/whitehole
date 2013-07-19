<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$uuid=$_GET['uuid'];
$mount_path=$_GET['mount_path'];

$query="select count(*) from vm_template;";
$result=@mysql_query($query);
$data=mysql_fetch_row($result);
$count_templates=$data['0'];
if ($count!=0) {
	alert_msg("Some template files is exist.......!!!!!");
}

$query="delete from secondary_storage where uuid='$uuid'";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
} else {
	run_ssh_key('localhost','root',"umount $mount_path");
	$sed_mount_path=ereg_replace('/','\/',$mount_path);
	run_ssh_key('localhost','root',"sed -i '/$sed_mount_path/d' /etc/fstab");
	echo ("
		<script language=\"javascript\">
			location.href=\"view_secondary_storage.php\"
		</script>
	");
}
?>
