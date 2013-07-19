<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$begin=$_POST['begin'];
$end=$_POST['end'];

$array_begin=explode('.',$begin);
$array_end=explode('.',$end);

$c_class_begin="$array_begin[0].$array_begin[1].$array_begin[2]";
$c_class_end="$array_end[0].$array_end[1].$array_end[2]";

if($c_class_begin!=$c_class_end) {
	alert_msg_close("Input Range-value is invalid.........!!! c-classh subnet is invalid");
} else {
	$range_begin=$array_begin['3'];
	$range_end=$array_end['3'];
	if ($range_begin>2 && $range_end<256 && $range_end>$range_begin) {
		for ($i=$range_begin;$i<=$range_end;$i=$i+1) {
			$ip_address="$c_class_begin.$i";
			$query="insert into network_pool values ('$ip_address','0','','')";
			$result=@mysql_query($query);
			if(!$result) {
				Query_Error();
			}
		}
	}
	echo ("
		<script language=\"javascript\">
			location.href=\"view_network_pool.php\"
		</script>
	");
}
?>
