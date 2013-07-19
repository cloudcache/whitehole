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
$snapshot_time=$_GET['snapshot_time'];
?>
<html>
<head>
	<link rel="stylesheet" href="../jquery/css/cupertino/jquery-ui-1.10.3.custom.css" />
	<script src="../jquery/js/jquery-1.9.1.js"></script>
	<script src="../jquery/js/jquery-ui-1.10.3.custom.js"></script>

<script>
function waiting()
{
	alert("[확인]을 누르고 잠시만 기다려 주세요.\n\n(데이터 용량에 따라 대기시간이 길어질 수 있습니다.)\n(Progress 표시는 준비중)");
	return true;
}
</script>

</head>
<body>

<center><b>Create Template from <font color=blue><?=$vm_name?></font></b></center>
<center><font color=red>(주의) Data-Volume은 제외한, OS HDD만 해당.</font></center>
<p>
<form name=add_template method=post action=proc-add_my_template.php onsubmit="waiting()">
<table align=center width=600 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Template Name
		<td align=center>
			<input type=text name=name size=50 maxlength=100>
			<input type=hidden name=uuid value=<?=$vm_uuid?>>
			<input type=hidden name=snapshot_time value=<?=$snapshot_time?>>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Description
		<td align=center><input type=text name=description size=50 maxlength=100>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Public
		<td align=center><input type=checkbox name=chk_public value=1 checked>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>
</body>
</html>
