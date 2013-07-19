<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$name=$_POST['name'];
$description=$_POST['description'];
$os_type=$_POST['chk_os_type'];
$url=$_POST['url'];

$create_time=time();
$iso_path="/home/mnt/sec/iso";
$uuid=rtrim(shell_exec("uuidgen"));

$iso_file="$iso_path/$uuid.iso";
$return=rtrim(run_ssh_key('localhost','root',"mkdir -p $iso_path; wget $url -O $iso_file 2> /dev/null; echo $?"));
if ((int)$return!=0) {
	run_ssh_key('localhost','root',"rm -f $iso_file");
	alert_msg("Can't download ISO image... Check the URL.");
	exit;
}

$query="insert into iso value ('$uuid','$create_time','$name','$description','$os_type','$url')";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
} else {
	echo ("
		<script language=\"javascript\">
			location.href=\"view_iso.php\"
		</script>
	");
}
?>
