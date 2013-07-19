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

$name=$_GET['name'];
$node=$_GET['node'];
$vnc_port=$_GET['vnc_port'];
if ($vnc_port=="-1") {
	echo ("
		<script language=\"javascript\">
			alert(\"Console 화면 포트가 비활성(-1) 상태 입니다. VM 인스턴스가 정상 가동 중인지 확인 하십시오.\");
			top.close();
		</script>
	");
	exit;
}
$uuid=$_GET['uuid'];
$vnc_port_new=$vnc_port+50000;
$allow_ip=$_SERVER['REMOTE_ADDR'];


/*
echo $name."<br>";
echo $node."<br>";
echo $vnc_port."<br>";
echo $vnc_port_new."<br>";
echo $allow_ip."<br>";
*/

## 내부 사설망 제약으로 임시로 클라이언트 아이피를 고정
run_ssh_key($node,'root',"/usr/bin/screen -d -m socat TCP4-LISTEN:$vnc_port_new TCP:localhost:$vnc_port");
#run_ssh_key($node,'root',"/usr/bin/screen -d -m socat TCP4-LISTEN:$vnc_port_new,range=$allow_ip/32 TCP:localhost:$vnc_port");
?>


<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="style01.css" type="text/css">
</head>
<body>

<table width=600>
	<tr>
		<td bgcolor=d8fff7><b><center>VM Name
		<td colspan=3><center><?=$name?> (<?=$uuid?>)
</table>
<APPLET CODE="VncViewer.class" ARCHIVE="vnc/VncViewer.jar" WIDTH="1200" HEIGHT="1000">
  <PARAM NAME="PORT" VALUE="<?=$vnc_port_new?>">
  <PARAM NAME="HOST" VALUE="<?=$node?>">
</APPLET>


</body>
</html>
