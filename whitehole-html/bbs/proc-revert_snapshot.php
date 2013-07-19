<?
$g4_path = ".."; // common.php 의 상대 경로
include_once("$g4_path/common.php");
//include_once("../_head.php");

## 사용자 가입여주 체크
if (!$member[mb_id] || $member['mb_level']<3){
    $msg = "비회원 및 미승인 회원은 이 게시판에 권한이 없습니다.\\n\\n관리자에게 문의 바랍니다.";
    if ($cwin)
        alert_close($msg);
    else
        //alert($msg, "./login.php?wr_id=$wr_id{$qstr}&url=".urlencode("./board.php?bo_table=$bo_table&wr_id=$wr_id"));
        alert($msg, "$g4_path/bbs/login.php?wr_id=$wr_id{$qstr}&url=$g4_path/index.php");
}
?>

<?
include "../db_conn.php";
include "../functions.php";
?>

<?
$vm_uuid=$_GET['vm_uuid'];
$vm_name=$_GET['vm_name'];
$create_time=$_GET['create_time'];
$target_node=$_GET['target_node'];
$vnc_port=$_GET['vnc_port'];

$res=libvirt_connect("qemu+ssh://root@$tatget_node/system","0");
$dom=libvirt_domain_lookup_by_name($res,$vm_name);
$snapshot_res=libvirt_domain_snapshot_lookup_by_name($dom, $create_time);
if (!$snapshot_res) {
	alert_msg("[Error] 지정된 스냅샷이 존재하지 않습니다.");
	exit;
}

$result=libvirt_domain_snapshot_revert($snapshot_res);
if (!$snapshot_res) {
	alert_msg("[Error] Revert에 실패 했습니다.");
	exit;
}
libvirt_domain_create($dom);

#run_ssh_key($target_node,'root',"virsh snapshot-revert $vm_name $create_time");

$xml=libvirt_domain_get_xml_desc($dom,"");
$json=json_encode(new SimpleXMLElement($xml));
$xml_array=json_decode($json,TRUE);
$vnc_port=@$xml_array['devices']['graphics']['@attributes']['port'];

$query="update info_vm set vnc='$vnc_port' where uuid='$vm_uuid'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}

?>

<script language="javascript">
	location.href="edit_snapshots.php?vm_uuid=<?=$vm_uuid?>&vm_name=<?=$vm_name?>&target_node=<?=$target_node?>&vnc_port=<?=$vnc_port?>&pre_result=yes"
</script>
