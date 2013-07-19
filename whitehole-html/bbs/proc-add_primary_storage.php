<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$host=$_POST['host'];
$fs_type=$_POST['fs_type'];
$export_path=$_POST['export_path'];
$create_time=time();
$mount_path="/home/mnt/pri";
$uuid=rtrim(shell_exec("uuidgen"));

/*
echo $host;
echo $fs_type;
echo $export_path;
echo $create_time;
echo $mount_path;
echo $uuid;
exit;
*/


run_ssh_key('localhost','root',"test ! -d $mount_path && mkdir -p $mount_path");

$return=rtrim(run_ssh_key('localhost','root',"mount -t $fs_type $host:$export_path $mount_path; echo $?"));
if ($return==0) {
	run_ssh_key('localhost','root',"echo '$host:$export_path $mount_path $fs_type defaults,_netdev 0 0' >> /etc/fstab");
	$query="insert into primary_storage value ('$uuid','$host','$fs_type','$export_path','$mount_path','$create_time','','','','')";
	$result=@mysql_query($query);
	if (!$result) {
		Query_Error();
	}
	echo ("
		<script language=\"javascript\">
			location.href=\"view_primary_storage.php\"
		</script>
	");
} else {
	alert_msg("Error : Can't mount Primary-Storage");
	exit;
}
?>
