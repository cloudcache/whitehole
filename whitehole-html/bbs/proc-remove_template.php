<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$template_path="/home/mnt/sec/templates";
$primary_base_path="/home/mnt/pri/base";

$uuid=$_GET['uuid'];

$query="delete from vm_template where uuid='$uuid'";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
} else {
	run_ssh_key('localhost','root',"rm -f $template_path/$uuid $primary_base_path/$uuid");
	echo ("
		<script language=\"javascript\">
			location.href=\"view_template.php\"
		</script>
	");
}
?>
