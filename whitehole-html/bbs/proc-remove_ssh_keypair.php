<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$uuid=$_GET['uuid'];
$used_count=$_GET['used_count'];

/*
echo $uuid;
echo "<br>";
echo $used_count;
exit;
*/

$path_key="/home/mnt/sec/ssh-keypair/$loguser";

$error_message="Can't Remove!!! Because... This SSH-KeyPair is Used by VM.....";

if ($used_count != 0) {
	alert_msg($error_message);
	exit;
} else {
	$query="delete from ssh_keypair where uuid='$uuid'";
	$result=@mysql_query($query);
	if (!$result) {
		Query_Error();
	} else {
		run_ssh_key('localhost','root',"rm -f $path_key/$uuid*");
		echo ("
			<script language=\"javascript\">
				location.href=\"view_ssh_keypair.php\"
			</script>
		");
	}
}

?>
