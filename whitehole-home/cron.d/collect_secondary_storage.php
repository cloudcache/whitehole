<?

include "/var/www/html/db_conn.php";
include "/var/www/html/functions.php";

//$ssh_host = "192.168.100.122";

$query="select uuid,mount_path from secondary_storage";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

while ($data=@mysql_fetch_row($result)) {
	$uuid=$data[0];
	$mount_path=$data[1];
	$return=explode(' ',shell_exec("df -Tm | grep '$mount_path' | awk '{print $3,$4,$5}'"));
	$total=$return[0]/1000;
	$used=$return[1]/1000;
	$free=$return[2]/1000;
	$update_time=time();

//echo $free;
//exit;

	$query="update secondary_storage set update_time='$update_time',total='$total',used='$used',free='$free' where uuid='$uuid'";
//	echo $query;

	$result_insert=@mysql_query($query);
	if (!$result_insert) {
		Query_Error();
	} 
}

?>
