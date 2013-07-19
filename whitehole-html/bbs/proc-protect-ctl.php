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

///////////////

$job=$_GET['job'];
$uuid=$_GET['uuid'];
$order=$_GET['order'];

if ($job=="on") {
	$query="update info_vm set protect='1' where uuid='$uuid'";
} else {
	$query="update info_vm set protect='0' where uuid='$uuid'";
}
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
?>

<script language="javascript">
	location.href="view_vm.php?order=<?=$order?>"
</script>
