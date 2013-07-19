<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$ip_address=$_GET['ip_address'];
#echo $ip_address;
#exit;

$query="update network_pool set used='0',vm='',account='' where ip_address='$ip_address'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
echo ("
	<script language=\"javascript\">
		location.href=\"view_network_pool.php\"
	</script>
");
?>
