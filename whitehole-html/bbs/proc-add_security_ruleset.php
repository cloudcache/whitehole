<? include_once("./_head.whitehole.php"); ?>

<?
include "../db_conn.php";
include "../functions.php";
?>

<?
$create_time=time();
$uuid=$_GET['uuid'];
$rule_name=$_GET['rule_name'];
$account=$_GET['account'];
$protocol=$_POST['protocol'];
$info_src=explode('/',$_POST['info_src']);
$src_ip=$info_src['0'];
$src_ip_mask=$info_src['1'];
if (!$src_ip_mask) {
	if ($src_ip=="0.0.0.0") {
		$src_ip_mask=0;
	} else {
		$src_ip_mask=32;
	}
}
if ($protocol=="icmp") {
	$dst_port_start="";
	$dst_port_end="";
} else {
	$dst_port_start=$_POST['dst_port_start'];
	$dst_port_end=$_POST['dst_port_end'];
}
$action=$_POST['action'];


@mysql_query("set autocommit=0");
@mysql_query("begin");

$query="insert into security_ruleset values ('','$create_time','$uuid','$rule_name','$account','$protocol','$src_ip','$src_ip_mask','$dst_port_start','$dst_port_end','$action')";
$result=@mysql_query($query);
if (!$result) {
    @mysql_query("rollback");
	alert_msg("[Error] 트랜잭션 실패 -> 모든 작업 롤백");
} else {
	@mysql_query("commit");
############
$path_nwfilter_xml="/home/mnt/sec/xml-nwfilter";
$security_group_uuid=$uuid;

$query_vm="select uuid,node from info_vm where security_group_uuid='$security_group_uuid' and status='1'";
$result_vm=@mysql_query($query_vm);
if (mysql_num_rows($result_vm)>0) {
	while ($data_vm=mysql_fetch_row($result_vm)) {
		$vm_uuid=$data_vm['0'];
		$target_node=$data_vm['1'];
include "include-apply_security_group.php";
	}
}
############
    echo ("
        <script language=\"javascript\">
            location.href=\"edit_security_group.php?rule_name=$rule_name&uuid=$uuid&account=$account\"
        </script>
    ");
}
?>
