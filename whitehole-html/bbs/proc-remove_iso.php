<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$iso_path="/home/mnt/sec/iso";

$uuid=$_GET['uuid'];

$query="delete from iso where uuid='$uuid'";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
} else {
	run_ssh_key('localhost','root',"rm -f $iso_path/$uuid.iso");
	echo ("
		<script language=\"javascript\">
			location.href=\"view_template.php\"
		</script>
	");
}
?>
