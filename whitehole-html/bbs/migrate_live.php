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

<html>
<head>
	<title>Whitehole - Add VM</title>
<!--	<link rel="stylesheet" href="style01.css" type="text/css"> -->
    <link rel="stylesheet" href="../jquery/css/cupertino/jquery-ui-1.10.3.custom.css" />
    <script src="../jquery/js/jquery-1.9.1.js"></script>
    <script src="../jquery/js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(function() {
  $("#list").accordion({
        active: 0,
        autoHeight: false,
        animate: {
                easing: "linear",
                duration: 100,
                down: {
                        easing: "easeOutBounce",
                        duration: 300
                }
        }
      });
});

function accordionDestroy() {
    $("#list").accordion("destroy");
}
</script>
</head>
<body>

<?
$order = $_GET['order'];
$vm_ip = $_GET['vm_ip'];
$vm_name = $_GET['vm_name'];
$src_host_ip = $_GET['src_host_ip'];
$cpu = $_GET['cpu'];
$mem = $_GET['mem'];
$vm_uuid = $_GET['vm_uuid'];

#echo $name;
#echo "<br>";
#echo $src_node;
?>

<form name=add_new_vm method=post action=proc-migrate_live.php?order=<?=$order?>>
<table align=center width=500 border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Source Info (Host/VM)
		<td align=center><font color=ff00ff><b><?=$vm_name?></font> (<?=$vm_ip?>)</b><br>Now, on <?=$src_host_ip?></font>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Migrate to Host
		<td align=center>
			<select name=dst_host_ip>
<?

#$query="select uuid,description,size_verify,hypervisor,bits,os_type from vm_template";
$query="select ip_address,hostname from info_node where not ip_address='$src_host_ip' and free_sys_cpu>$cpu and free_sys_mem>$mem order by hostname asc";

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

if (!$result) {
	alert_msg("Not exist Physical Host that migration possible!!! Retry later!!");
	exit;
}

while ($data=@mysql_fetch_row($result)) {
//print_r($data);
	$dst_host_ip=$data['0'];
	$hostname=$data['1'];
		echo("<option value=$dst_host_ip>$hostname ($dst_host_ip)</option>");
		echo("<input type=hidden name=vm_ip value=$vm_ip>");
		echo("<input type=hidden name=vm_name value=$vm_name>");
		echo("<input type=hidden name=src_host_ip value=$src_host_ip>");
		echo("<input type=hidden name=vm_uuid value=$vm_uuid>");
}
?>
	</tr>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>

</body>
</html>
