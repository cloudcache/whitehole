<?

include "/var/www/html/db_conn.php";
include "/var/www/html/functions.php";

//$ssh_host = "192.168.100.122";

$query="select * from info_vm";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

while ($data=@mysql_fetch_row($result)) {
	$uuid=$data['0'];
	$create_time=$data['1'];
	$sshkey_uuid=$data['2'];
	$sshkey_desc=$data['3'];
	$ip_address=$data['4'];
	$name=$data['5'];
	$cpu=$data['6'];
	$memory=$data['7'];
	$mac=$data['8'];
	$bits=$data['9'];
	$hypervisor=$data['10'];
	$node=$data['11'];
	$vnc=$data['12'];
	$account=$data['13'];
	$data_volume=$data['14'];
	$use_for=$data['15'];
	$status=$data['16'];
//	print_r($data);

	$conn=libvirt_connect("qemu+ssh://root@$node/system");
	$resource=libvirt_domain_lookup_by_name($conn,$name);
	$isActive=libvirt_domain_is_active($resource);
//	echo $isActive;

	$query="update info_vm set status='$isActive' where name='$name'";
//	echo $query;

	$result_insert=@mysql_query($query);
	if (!$result_insert) {
		Query_Error();
	} 
}

?>
