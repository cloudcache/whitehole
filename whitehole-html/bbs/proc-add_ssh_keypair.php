<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

///////////////

$create_time=time();
$description=$_POST['description'];
$uuid=rtrim(shell_exec("uuidgen"));

/*
$query="select count(description) from ssh_keypair where uuid='$uuid' and description='$description'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}

$date=@mysql_fetch_row($result);
if (!$description || $data['0'] != "0") {
*/
if (!$description) {
	alert_msg_close("Error : Description is blank or alread exist.... Check the value...(Your value is '$description')");
	echo ("
		<script language=\"javascript\">
			location.href=\"add_ssh_keypair.php\"
		</script>
	");
	exit;
}

$path_key="/home/mnt/sec/ssh-keypair/$loguser";

$return_chk_dir=rtrim(run_ssh_key('localhost','root',"test -d $path_key; echo $?"));
if ((int)$return_chk_dir!=0) {
	run_ssh_key('localhost','root',"mkdir -p $path_key");
}

$return_chk_gen=rtrim(run_ssh_key('localhost','root',"ssh-keygen -t rsa -P '' -f $path_key/$uuid > /dev/null; echo $?"));
if ("$return_chk_gen"=="0") {
	run_ssh_key('localhost','root',"chmod 644 $path_key/$uuid");
	$query="insert into ssh_keypair values ('$uuid','$loguser','$description','0','$create_time')";
	$result=@mysql_query($query);
	if (!$result) {
		Query_Error();
	}
	echo ("
		<script language=\"javascript\">
			location.href=\"view_ssh_keypair.php\"
		</script>
	");
} else {
	alert_msg_close("Error : Failed to create ssh-keypair (ssh-keygen -t rsa -P '' -f $path_key)");
	exit;
}
$return_chk_gen=rtrim(run_ssh_key('localhost','root',"chown www-data:www-data $path_key/$uuid*"));
?>
