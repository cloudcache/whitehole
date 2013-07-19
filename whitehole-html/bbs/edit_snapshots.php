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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Snapshot List</title>
	<link rel="stylesheet" href="style01.css" type="text/css">
<script type="text/javascript">
function goPage(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=950, height=700";
   window.open(url, name, winFeatures); 
}
function goPageMyTemplate(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=650, height=230";
   window.open(url, name, winFeatures); 
}
</script>

</head>
<body>

<?
$vm_uuid=$_GET['vm_uuid'];
$vm_name=$_GET['vm_name'];
$target_node=$_GET['target_node'];
$vnc_port=$_GET['vnc_port'];
#echo $target_node;
#exit;

$pre_result=$_GET['pre_result'];
if (isset($_GET['pre_result'])) {
	if ($_GET['pre_result']=="yes") {
?>
		<script type="text/javascript">
			alert("[Success] Revert 성공");
		</script>
		<script type="text/javascript">
			goPage('<?=$vm_uuid?>','vncviewer.php?name=<?=$vm_name?>&node=<?=$target_node?>&vnc_port=<?=$vnc_port?>&uuid=<?=$vm_uuid?>')
		</script>
		<script language="javascript">
			location.href="edit_snapshots.php?vm_uuid=<?=$vm_uuid?>&vm_name=<?=$vm_name?>&target_node=<?=$target_node?>"
		</script>
<?
	} else {
?>
		<script type="text/javascript">
			alert("[Error] Revert 실패");
		</script>
<?
	}
}


$query="select * from snapshots where vm_uuid='$vm_uuid' order by create_time desc";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
?>

<center><b>VM Name: <font color=blue><?=$vm_name?></font></b></center>

<b>&nbsp;신규 스냅샷 생성</b>
<p>
<form name=create_snapshot method=post action='proc-create_snapshot.php?vm_uuid=<?=$vm_uuid?>&vm_name=<?=$vm_name?>&target_node=<?=$target_node?>&vnc_port=<?=$vnc_port?>'>
<table align=center width=98% border=0 cellspacing=0 cellpadding=5>
	<tr>
		<td align=center>Description</td>
		<td align=center>
			<input type=text name=description size=50 maxlength=49>
		</td>
	</td>
	<tr>
		<td align=center colspan=2>
			스냅샷 생성을 하시겠습니까? (재부팅 과정 필요)&nbsp;&nbsp;
			<input type=submit name=btn1 value="확인">
		</td>
	</td>
</table>
</form>

<br>
<br>
<b>&nbsp;스냅샷 현황</b>
<p>
<table align=center width=98% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr>
		<td bgcolor="efffff" align=center><b>Created</td>
		<td bgcolor="efffff" align=center width=350><b>Description</td>
		<td bgcolor="efffff" align=center width=90><b>to Template</td>
		<td bgcolor="efffff" align=center width=80><b>Revert</td>
		<td bgcolor="efffff" align=center width=80><b>Terminate</td>
	</tr>
<?
while ($data = mysql_fetch_row($result)) {
	$num = $data['0'];
	$create_time = $data['1'];
	$description = $data['3'];
?>
	<tr>
		<td align=center><?=date("Y/m/d H:i:s",$create_time)?><br>(<?=$create_time?>)
		<td align=center><?=$description?>
		<td><center><input style='width: 80' type="button" onClick="javascript:goPageMyTemplate('My-Template-<?=$vm_uuid?>','add_my_template.php?vm_uuid=<?=$vm_uuid?>&vm_name=<?=$vm_name?>&snapshot_time=<?=$create_time?>')" value="to Template"></center>
		<td><center><input style='width: 70' type="button" onClick="location.href='proc-revert_snapshot.php?vm_uuid=<?=$vm_uuid?>&create_time=<?=$create_time?>&target_node=<?=$target_node?>&vm_name=<?=$vm_name?>&vnc_port=<?=$vnc_port?>'" value="Revert"></center>
		<td><center><input style='width: 70' type="button" onClick="location.href='proc-remove_snapshot.php?vm_uuid=<?=$vm_uuid?>&create_time=<?=$create_time?>&target_node=<?=$target_node?>&vm_name=<?=$vm_name?>&vnc_port=<?=$vnc_port?>'" value="Terminate"></center>
	</tr>
<?
}
?>
</table>

</body>
</html>
