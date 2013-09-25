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

$rule_name=$_GET['rule_name'];
$uuid=$_GET['uuid'];
$vm_uuid=$_GET['vm_uuid'];
$account=$_GET['account'];

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Whitehole - Network Pool</title>
	<link rel="stylesheet" href="style01.css" type="text/css">

<script type="text/javascript">
function goPage(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=500, height=500";
   window.open(url, name, winFeatures); 
}
</script>
</head>
<body>

<center>
	<b>Security Group Name: <font color=green><?=$rule_name?></font></b>
</center>

<form name=add_security_groupm method=post action='proc-add_security_ruleset.php?uuid=<?=$uuid?>&vm_uuid=<?=$vm_uuid?>&rule_name=<?=$rule_name?>&account=<?=$account?>'>
<b>신규 정책 추가</b>
<!--
<a href="sg_guide.php" target=_blank><font size=2>[작성 가이드]</font></a>
-->
<input style='width: 90' type="button" onClick="javascript:goPage('sg_guide','sg_guide.php')" value="작성 가이드"><br>
<p>
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center bgcolor="efffff">
		<td align=center>
			<b>프로토콜
		</td>
		<td align=center>
			<b>출발지 정보 CIDR<br>ex) 192.168.10.0/24, 1.2.3.4
		</td>
		<td align=center>
			<b>시작 포트<br>(0 ~ 65534)
		</td>
		<td align=center>
			<b>종료 포트<br>(0 ~ 65534)
		</td>
		<td align=center>
			<b>정책
		</td>
		<td align=center>
			<b>확인
		</td>
	<tr align=center bgcolor="efffff">
		<td align=center>
			<select name=protocol>
				<option value=tcp><b>TCP</option>
				<option value=udp><b>UDP</option>
				<option value=icmp><b>ICMP</option>
		</td>
		<td align=center>
			<input type=text name=info_src size=35 maxlength=34>
		</td>
		<td align=center>
			<input type=text name=dst_port_start size=10 maxlength=9>
		</td>
		<td align=center>
			<input type=text name=dst_port_end size=10 maxlength=9>
		</td>
		<td align=center>
			<select name=action>
				<option value=accept><b>ACCEPT</option>
				<option value=reject><b>REJECT</option>
				<option value=drop><b>DROP</option>
		</td>
		<td align=center width=100>
			<input type=submit name=btn1 value="Submit">
		</td>
	</tr>
</table>
</form>

<br>
<br>

<b>현재 정책 상태</b><p>
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center bgcolor="efffff">
		<!--
		<td align=center><b>Number
		<td align=center><b>Created
		<td align=center><b>UUID
		-->
		<td align=center><b>프로토콜
		<td align=center><b>출발지 IP/Netmask (CIDR)
		<td align=center><b>허용 포트 범위
		<td align=center><b>정책
		<td align=center><b>삭제
	</tr>
<?
$query="select * from security_ruleset where uuid='$uuid'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}

while ($data_sg=@mysql_fetch_row($result)) {
		$num=$data_sg['0'];
		$create_time=$data_sg['1'];
		$uuid=$data_sg['2'];
		$rule_name=$data_sg['3'];
		$account=$data_sg['4'];
		$protocol=$data_sg['5'];
		$src_ip=$data_sg['6'];
		$src_ip_mask=$data_sg['7'];
		$dst_port_start=$data_sg['8'];
		$dst_port_end=$data_sg['9'];
		$action=$data_sg['10'];
?>
	<tr align=center>
		<!--
		<td><?=$num?>
		<td><?=date('Y/m/d H:i:s',$create_time)?>
		<td><?=$uuid?>
		-->
		<td><?=strtoupper($protocol)?>
		<td>
<?
if ($src_ip=="0.0.0.0") {
		echo ("Any");
} else {
	if ($src_ip_mask=="32") {
			echo ("$src_ip");
	} else {
			echo ("$src_ip/$src_ip_mask");
	}
}
?>
		<td>
<?
if ($protocol=="icmp") {
		echo ("(N/A)");
} else {
	if ($dst_port_start==$dst_port_end) {
		echo ("$dst_port_start");
	} else {
		echo ("$dst_port_start ~ $dst_port_end");
	}
}
?>
		<td><?=strtoupper($action)?>
		<td><input style='width: 60' type="button" onClick="location.href='proc-remove_security_ruleset.php?vm_uuid=<?=$vm_uuid?>&num=<?=$num?>&uuid=<?=$uuid?>&rule_name=<?=$rule_name?>&account=<?=$account?>'" value="Remove"><br>
	</tr>
<?
}
?>
</table>

<p>
<?
if ($loguser=="admin") {
?>
	<center><input style='width: 100' type="button" onClick="location.href='proc-apply_security_group.php?vm_uuid=<?=$vm_uuid?>&uuid=<?=$uuid?>&rule_name=<?=$rule_name?>&account=<?=$account?>'" value="Policy Reload"></center>
<?
}
?>



</body>
</html>
