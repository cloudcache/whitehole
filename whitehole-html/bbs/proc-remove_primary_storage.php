<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$uuid=$_GET['uuid'];
$mount_path=$_GET['mount_path'];

$query="delete from primary_storage where uuid='$uuid'";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
} else {
	run_ssh_key('localhost','root',"umount $mount_path");
	$sed_mount_path=ereg_replace('/','\/',$mount_path);
	run_ssh_key('localhost','root',"sed -i '/$sed_mount_path/d' /etc/fstab");
	echo ("
		<script language=\"javascript\">
			location.href=\"view_primary_storage.php\"
		</script>
	");
}
?>
