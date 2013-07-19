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

$template_name=$_POST['name'];
$vm_uuid=$_POST['uuid'];
$snapshot_time=$_POST['snapshot_time'];
$description=$_POST['description'];
$public=$_POST['chk_public'];

$query="select * from info_vm where uuid='$vm_uuid'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}

while ($data = mysql_fetch_row($result)) {
	#$uuid = $data['0'];
	#$vm_create_time = $data['1'];
	#$vm_sshkey_uuid = $data['2'];
	#$vm_sshkey_desc = $data['3'];
	#$vm_ip_address = $data['4'];
	#$vm_name = $data['5'];
	#$vm_cpu = $data['6'];
	#$vm_memory = $data['7'];
	#$vm_memory_h = $data['7']/1024;
	#$vm_mac = $data['8'];
	$vm_bits = $data['9'];
	$vm_hypervisor = $data['10'];
	#$vm_node = $data['11'];
	#$vm_vnc_port = $data['12'];
	#$vm_account = $data['13'];
	#$vm_data_volume = $data['14'];
	$vm_os_type = $data['15'];
	#$vm_hostname = $data['16'];
	#$vm_status = $data['17'];
	#$vm_security_group_uuid = $data['18'];
	#$vm_origin = explode("___", $data['19']);
	#$vm_origin_type = $origin['0'];
	#$vm_origin_uuid = $origin['1'];
}

$featured="1";
$account=$loguser;
$bootable="1";

$template_create_time=time();
$pri_path="/home/mnt/pri/instances/";
$template_path="/home/mnt/sec/templates";
$template_uuid=rtrim(shell_exec("uuidgen"));

$vdi_file="$template_path/$template_uuid";

$return=rtrim(run_ssh_key('localhost','root',"qemu-img convert -s $snapshot_time -f qcow2 $pri_path/$vm_uuid -O qcow2 $vdi_file; echo $?"));
if ((int)$return!=0) {
	run_ssh_key('localhost','root',"rm -f $vdi_file");
	alert_msg("Can't download template-image... Check the URL.");
	exit;
}

$img_info=explode(' ',run_ssh_key('localhost','root',"qemu-img info $vdi_file | tr '\n' ' '"));

$format=$img_info['4'];
$size_virtual=ereg_replace('G','',$img_info['7']);
$size_real=ereg_replace('G','',$img_info['12']);

$size_verify=rtrim(run_ssh_key('localhost','root',"ls -l $vdi_file | cut -d' ' -f5"));


$query="insert into vm_template value ('$template_uuid','$template_name','$public','$featured','$vm_hypervisor','$vm_bits','from VM','$format','$template_create_time','$account','$description','$bootable','$size_virtual','$size_real','$size_verify','$vm_os_type')";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
} else {
?>
	<script language="javascript">
		alert("[Success] Template 생성 성공\n[My Template] 메뉴에서 확인 하세요.");
		window.close();
	</script>
<?
}
?>
