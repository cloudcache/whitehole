<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once("$g4[path]/lib/outlogin.lib.php");
include_once("$g4[path]/lib/poll.lib.php");
include_once("$g4[path]/lib/visit.lib.php");
include_once("$g4[path]/lib/connect.lib.php");
include_once("$g4[path]/lib/popular.lib.php");
include_once("$g4[path]/lib/latest.lib.php");
include_once("$g4[path]/head.sub.php");
//print_r2(get_defined_constants());

// 사용자 화면 상단과 좌측을 담당하는 페이지입니다.
// 상단, 좌측 화면을 꾸미려면 이 파일을 수정합니다.
$sql = "select count(*) as cnt from $g4[login_table] where mb_id <> '$config[cf_admin]'";
$row = sql_fetch($sql);
$current_connect = $row[cnt];

// 읽지 않은 쪽지가 있다면
$memo_not_read = 0;
if ($is_member) {
    $sql = " select count(*) as cnt from $g4[memo_table] where me_recv_mb_id = '$member[mb_id]' and me_read_datetime = '0000-00-00 00:00:00' ";
    $row = sql_fetch($sql);
    $memo_not_read = $row['cnt'];
}
?>

<link rel="stylesheet" href="<?=$g4['path']?>/theme/<?=$g4['mw_lite_theme']?>/style.css" type="text/css"/>

<style type="text/css">
#head .mw-index-menu-bar { background:url(<?=$g4[path]?>/theme/<?=$g4['mw_lite_theme']?>/img/mm.png); }
#head .mw-index-menu-div { background:url(<?=$g4[path]?>/theme/<?=$g4['mw_lite_theme']?>/img/md.png) center no-repeat; }
#head .mw-index-menu-select1 { background:url(<?=$g4[path]?>/theme/<?=$g4['mw_lite_theme']?>/img/msm.png); }
#head .mw-index-menu-select2 { background:url(<?=$g4[path]?>/theme/<?=$g4['mw_lite_theme']?>/img/msl.png) top left no-repeat; }
#head .mw-index-menu-select3 { background:url(<?=$g4[path]?>/theme/<?=$g4['mw_lite_theme']?>/img/msr.png) top right no-repeat; }
#head .mw-index-menu-bar .mw-drop-menu div { background:url(<?=$g4[path]?>/img/dot.gif) 0 7px no-repeat; }
#sm .sm_sub { background:url(<?=$g4[path]?>/img/menu.gif) left top no-repeat;  }
</style>

<link rel="stylesheet" href="../jquery/css/cupertino/jquery-ui-1.10.3.custom.css" />
<script src="../jquery/js/jquery-1.9.1.js"></script>
<script src="../jquery/js/jquery-ui-1.10.3.custom.js"></script>
<script>
function waiting()
{
	alert("[확인]을 누르고 잠시만 기다려 주세요.\n\n(Progress 표시는 준비중)");
	return true;
}
</script>
<script>
  $(function() {
    $( "#menu" ).menu();
  });
  $(function() {
    $( "#menu_admin" ).menu({
	});
  });
/* Dialog: Create VM from Template */
  $(document).ready(function() {
	$('#vm_create_template').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 600,
		height: 420,draggable: false,
		resizable: false
	});
	$('#link_vm_create_template').click(function(){
		$('#vm_create_template').dialog('open');
	});
  });
/* Dialog: Create VM from ISO */
  $(document).ready(function() {
	$('#vm_create_iso').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 600,
		height: 420,draggable: false,
		resizable: false
	});
	$('#link_vm_create_iso').click(function(){
		$('#vm_create_iso').dialog('open');
	});
  });
/* Dialog: Create SSH-Keypair */
  $(document).ready(function() {
	$('#ssh_create_key').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 500,
		height: 140,draggable: false,
		resizable: false
	});
	$('#link_ssh_create_key').click(function(){
		$('#ssh_create_key').dialog('open');
	});
  });
/* Dialog: Add Physical HOST */
  $(document).ready(function() {
	$('#add_physical_host').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 500,
		height: 200,draggable: false,
		resizable: false
	});
	$('#link_add_physical_host').click(function(){
		$('#add_physical_host').dialog('open');
	});
  });
/* Dialog: Add Template */
  $(document).ready(function() {
	$('#add_template').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 500,
		height: 410,draggable: false,
		resizable: false
	});
	$('#link_add_template').click(function(){
		$('#add_template').dialog('open');
	});
  });
/* Dialog: Add ISO */
  $(document).ready(function() {
	$('#add_iso').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 650,
		height: 250,draggable: false,
		resizable: false
	});
	$('#link_add_iso').click(function(){
		$('#add_iso').dialog('open');
	});
  });
/* Dialog: View Primary Storage */
  $(document).ready(function() {
	$('#view_storage_primary').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 550,
		height: 390,draggable: false,
		resizable: false
	});
	$('#link_view_storage_primary').click(function(){
		$('#view_storage_primary').dialog('open');
	});
  });
/* Dialog: Add Primary Storage */
  $(document).ready(function() {
	$('#add_storage_primary').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 550,
		height: 390,draggable: false,
		resizable: false
	});
	$('#link_add_storage_primary').click(function(){
		$('#add_storage_primary').dialog('open');
	});
  });
/* Dialog: View Secondary Storage */
  $(document).ready(function() {
	$('#view_storage_secondary').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 550,
		height: 390,draggable: false,
		resizable: false
	});
	$('#link_view_storage_secondary').click(function(){
		$('#view_storage_secondary').dialog('open');
	});
  });
/* Dialog: Add Secondary Storage */
  $(document).ready(function() {
	$('#add_storage_secondary').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 550,
		height: 390,draggable: false,
		resizable: false
	});
	$('#link_add_storage_secondary').click(function(){
		$('#add_storage_secondary').dialog('open');
	});
  });
/* Dialog: View Network Pool */
  $(document).ready(function() {
	$('#view_network_pool').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 600,
		height: 700,draggable: false,
		resizable: false
	});
	$('#link_view_network_pool').click(function(){
		$('#view_network_pool').dialog('open');
	});
  });
/* Dialog: Register Network Pool */
  $(document).ready(function() {
	$('#add_network_pool').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 600,
		height: 700,draggable: false,
		resizable: false
	});
	$('#link_add_network_pool').click(function(){
		$('#add_network_pool').dialog('open');
	});
  });
/* Dialog: Register Network Pool */
  $(document).ready(function() {
	$('#add_security_group').dialog({
		position: ['middle',100],
		autoOpen: false,
		width: 600,
		height: 170,draggable: false,
		resizable: false
	});
	$('#link_add_security_group').click(function(){
		$('#add_security_group').dialog('open');
	});
  });
</script>

<style>
  .ui-menu { width: 145px; }
</style>



<div id="mw-index">

<!-- 헤더 시작 -->
<div id="head">

<table border=0 cellpadding=0 cellspacing=0 style="margin:0 auto 0 auto;" align="center">
<tr>
<!--
<td class="logo"><a href="<?=$g4[path]?>"><img src="<?=$g4[path]?>/img/logo.png"></a></td>
-->
</tr>
</table>

<div class="mw-index-menu-bar">
    <div class="mw-index-menu-left"><img src="<?=$g4[path]?>/theme/<?=$g4['mw_lite_theme']?>/img/ml.png"></div>
    <!-- 그룹 메뉴 시작 -->
    <?
    $select_div_begin = "<div class='mw-index-menu-select1'><div class='mw-index-menu-select2'><div class='mw-index-menu-select3'>";
    $select_div_end = "</div></div></div>";

    $sql = "select * from $g4[group_table] order by gr_1";
    $qry = sql_query($sql);
    $cnt = mysql_num_rows($qry);

    if ($cnt == 1) {
        $sql = "select * from $g4[board_table] order by bo_order_search";
        $qry = sql_query($sql);
    }

    for ($i=0; $row=sql_fetch_array($qry); $i++)
    {
        if ($cnt == 1) {
            $menu_id = $bo_table;
            $row_id = $row[bo_table];
            $row_subject = $row[bo_subject];
            $menu_file = "board.php";
            $menu_type = "bo_table";
        }
        else {
            $menu_id = $gr_id;
            $row_id = $row[gr_id];
            $row_subject = $row[gr_subject];
            $menu_file = "group.php";
            $menu_type = "gr_id";
        }

        if ($i > 0) echo "<span class='mw-index-menu-div'></span>"; 

        if ($menu_id == $row_id) {
            $div_begin = $select_div_begin;
            $div_end = $select_div_end;
        } else {
            $div_begin = "<div class='mw-index-menu-item' gr_id='{$row_id}'>";
            $div_end = "</div>";
        }

	$group_link = (!empty($row['gr_2']))?$row['gr_2']:$g4['bbs_path'].'/'.'group.php?gr_id=' .$row['gr_id']; // 추가 
	echo "$div_begin<a href=\"$group_link\">$row[gr_subject]</a>$div_end"; // 수정
        //echo "$div_begin<a href=\"$g4[bbs_path]/$menu_file?$menu_type=$row_id\">$row_subject</a>$div_end";
    }

    $sql = "select * from $g4[group_table]  order by CAST(gr_1 AS SIGNED) asc ";
    $qry = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($qry); $i++) {
        ob_start();
        ?>
        <div id="mw-drop-menu-<?=$row[gr_id]?>" class="mw-drop-menu">
        <?
        $sql2 = "select * from $g4[board_table] where gr_id = '$row[gr_id]' order by bo_order_search asc";
        $qry2 = sql_query($sql2);
        for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
            if ($row2[bo_table] == $bo_table) $class = "sm_sub selected"; else $class = "sm_sub";
            ?><div><a href="<?=$g4[bbs_path]?>/board.php?bo_table=<?=$row2[bo_table]?>"><?=$row2[bo_subject]?></a></div><?
        }
        ?>
        </div> <!-- mw-drop-menu -->
        <?
        $drop_menu = ob_get_contents();
        ob_end_clean();

        if ($j>1) echo $drop_menu;
    } ?>

    <!-- 그룹 메뉴 끝 -->
    <div class="mw-index-menu-right"><img src="<?=$g4[path]?>/theme/<?=$g4['mw_lite_theme']?>/img/mr.png"></div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $(".mw-index-menu-item").mouseenter(function () {
        $(".mw-drop-menu").hide();
        gr_id = $(this).attr("gr_id");
        t = $(this).offset().top;
        l = $(this).offset().left;
        $("#mw-drop-menu-"+gr_id).css("top", t+30);
        $("#mw-drop-menu-"+gr_id).css("left", l);
        $("#mw-drop-menu-"+gr_id).show();
    });
    $(".mw-index-menu-bar").mouseleave(function () {
        $(".mw-drop-menu").hide();
    });
});
</script>



</div><!-- head -->

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
    <td valign="top" width="180">
        <div class="outlogin"><?=outlogin("mw_lite")?></div>

        <div id="sm">
            <div class="sm_border">
            <div class="sm_margin">
<?
/*
//print_r($member);
if ($member['mb_level']>=3) {
	echo "<b><font color=green>Virtual Machine</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_vm.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_vm.php\">Create (from Template)</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_vm_iso.php\">Create (from ISO)</a><br>";
	echo "<p>";
	echo "<b><font color=green>SSH Keypair</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_ssh_keypair.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_ssh_keypair.php\">Create</a><br>";
	echo "<p>";
}
if ($member['mb_level']>=10) {
	echo "<b><font color=brown>Physical Machine</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_nodes.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_node.php\">Add</a><br>";
	echo "<p>";
	echo "<b><font color=brown>Template</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_template.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_template.php\">Add</a><br>";
	echo "<p>";
	echo "<b><font color=brown>ISO Image</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_iso.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_iso.php\">Add</a><br>";
	echo "<p>";
	echo "<b><font color=brown>Primary Storage</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_primary_storage.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_primary_storage.php\">Add</a><br>";
	echo "<p>";
	echo "<b><font color=brown>Secondary Storage</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_secondary_storage.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_secondary_storage.php\">Add</a><br>";
	echo "<p>";
	echo "<b><font color=brown>Network Pool</font></b><br>";
	echo "-  <a href=\"$g4[bbs_path]/view_network_pool.php\">View</a><br>";
	echo "-  <a href=\"$g4[bbs_path]/add_network_pool.php\">Add</a><br>";
	echo "<p>";
}
*/
?>
<p>

<?
//print_r($member);
if ($member['mb_level']>=3) {
?>
<ul id="menu">
	<li>
		<a href="#"><b><font color=green>Virtual Machine</font></b></a>
		<ul>
			<li><a href="<?=$g4[bbs_path]?>/view_vm.php">View</a></li>
			<li>
				<a href="#">Create</a>
				<ul>
					<li><a href="#" id="link_vm_create_template">from Template</a></li>
					<li><a href="#" id="link_vm_create_iso">from ISO</a></li>
				</ul>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=green>SSH Keypair</font></b></a>
		<ul>
			<li><a href="<?=$g4[bbs_path]?>/view_ssh_keypair.php">View</a></li>
			<li><a href="#" id="link_ssh_create_key">Create</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=green>Security Group</font></b></a>
		<ul>
			<li><a href="<?=$g4[bbs_path]?>/view_security_group.php">View</a></li>
			<li><a href="#" id="link_add_security_group">Create</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=green>My Template</font></b></a>
		<ul>
			<li><a href="<?=$g4[bbs_path]?>/view_my_template.php">View</a></li>
		</ul>
	</li>
</ul>
<?
}
?>

<p>

<?
if ($member['mb_level']>=10) {
?>
<ul id="menu_admin">
	<li>
		<a href="#"><b><font color=brown>Physical Machine</font></b></a>
		<ul>
			<li><a href="<?=$g4[bbs_path]?>/view_nodes.php">View</a></li>
			<li><a href="#" id="link_add_physical_host">Add</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=brown>Template Image</font></b></a>
		<ul>
			<li><a href="<?=$g4[bbs_path]?>/view_template.php">View</a></li>
			<li><a href="#" id="link_add_template">Add</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=brown>ISO Image</font></b></a>
		<ul>
			<li><a href="<?=$g4[bbs_path]?>/view_iso.php">View</a></li>
			<li><a href="#" id="link_add_iso">Add</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=brown>Storage: PRI</font></b></a>
		<ul>
			<li><a href="#" id="link_view_storage_primary">View</a></li>
			<li><a href="#" id="link_add_storage_primary">Add</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=brown>Storage: SEC</font></b></a>
		<ul>
			<li><a href="#" id="link_view_storage_secondary">View</a></li>
			<li><a href="#" id="link_add_storage_secondary">Add</a></li>
		</ul>
	</li>
	<li>
		<a href="#"><b><font color=brown>Network Pool</font></b></a>
		<ul>
			<li><a href="#" id="link_view_network_pool">View</a></li>
			<li><a href="#" id="link_add_network_pool">Add</a></li>
		</ul>
	</li>
</ul>
<?
}
?>


<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->
<!-- Dialog 전체 시작 -->


<!-- Dialog 내용들 시작 : Create VM from Template-->
<p><div id="vm_create_template" title="Create New VM from Template">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<p>
<form name=add_new_vm method=post action="<?=$g4[bbs_path]?>/proc-add_vm.php" onsubmit="waiting()">
<table align=center width=95% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Hostname <font color=blue>(Optional)</font>
		<td align=center><input type=text name=hostname size=35 maxlength=20><br><font color=ff4500>[주의] 밑줄(_) 및 특수문자 사용 블가</font>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Template
		<td align=center>
			<select name=template_uuid>
<?

$query="select uuid,name from vm_template where public='1' or account='$loguser' or public='1' order by name desc";
#if ($loguser=="admin") {
#	$query="select uuid,description from vm_template where description not like 'Template-Vyatta%' order by name desc";
#	#$query="select uuid,description from vm_template order by name desc";
#} else {
#	$query="select uuid,description from vm_template where description not like 'Template-Vyatta%' order by name desc";
#}

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
while ($data=@mysql_fetch_row($result)) {
	$uuid=$data['0'];
	$name=$data['1'];
		echo("<option value=$uuid>$name</option>");
}
?>
	</tr>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Core
		<td align=center><select name=core>
			<option value=1><b>1 Core</option>
			<option value=2><b>2 Core</option>
			<option value=4><b>4 Core</option>
			<option value=8><b>8 Core</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Memory
		<td align=center><select name=memory>
			<option value=1024>1 GB</option>
			<option value=2048>2 GB</option>
			<option value=4096>4 GB</option>
			<option value=8192>8 GB</option>
			<option value=16384>16 GB</option>
        <tr align=center>
                <td align=center bgcolor=99CCFF><b>root-Volume
                <td align=center><select name=root_volume>
      		        <option value=8>8 GB</option>
               		<option value=50>50 GB</option>
                	<option value=100>100 GB</option>
                	<option value=300>300 GB</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Data-Volume
		<td align=center><select name=data_volume>
			<option value=0>None</option>
			<option value=100>100 GB</option>
			<option value=200>200 GB</option>
			<option value=500>500 GB</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>SSH KeyPair
		<td align=center><select name=ssh_keypair_uuid>
<?
$query="select uuid,description from ssh_keypair where account='$loguser'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
while ($data=@mysql_fetch_row($result)) {
	$ssh_keypair_uuid=$data['0'];
	$description=$data['1'];
		echo ("<option value='$ssh_keypair_uuid'>$description</option>");
}

?>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Security Group
		<td align=center><select name=security_group_uuid>
<?
$query="select uuid,rule_name from security_group where account='$loguser'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
while ($data=@mysql_fetch_row($result)) {
	$security_group_uuid=$data['0'];
	$rule_name=$data['1'];
		echo ("<option value='$security_group_uuid'>$rule_name</option>");
}
?>	
</table>
<p>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</form>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : Create VM from ISO-->
<p><div id="vm_create_iso" title="Create New VM from ISO">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<p>
<form name=add_new_vm method=post action="<?=$g4[bbs_path]?>/proc-add_vm_iso.php">
<table align=center width=95% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Hostname <font color=blue>(Optional)</font>
		<td align=center><input type=text name=hostname size=35 maxlength=20><br><font color=ff4500>[주의] 밑줄(_) 및 특수문자 사용 블가</font>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>ISO
		<td align=center>
			<select name=iso_uuid>
<?

$query="select uuid,description from iso order by name desc";

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
while ($data=@mysql_fetch_row($result)) {
	$uuid=$data['0'];
	$description=$data['1'];
		echo("<option value=$uuid>$description</option>");
}
?>
	</tr>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Core
		<td align=center><select name=core>
			<option value=1><b>1 Core</option>
			<option value=2><b>2 Core</option>
			<option value=4><b>4 Core</option>
			<option value=8><b>8 Core</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Memory
		<td align=center><select name=memory>
			<option value=1024>1 GB</option>
			<option value=2048>2 GB</option>
			<option value=4096>4 GB</option>
			<option value=8192>8 GB</option>
        <tr align=center>
                <td align=center bgcolor=99CCFF><b>root-Volume
                <td align=center><select name=root_volume>
      		        <option value=8>8 GB</option>
               		<option value=50>50 GB</option>
                	<option value=100>100 GB</option>
                	<option value=300>300 GB</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Data-Volume
		<td align=center><select name=data_volume>
			<option value=0>None</option>
			<option value=100>100 GB</option>
			<option value=200>200 GB</option>
			<option value=500>500 GB</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>SSH KeyPair
		<td align=center><select name=ssh_keypair_uuid>
<?
$query="select uuid,description from ssh_keypair where account='$loguser'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
while ($data=@mysql_fetch_row($result)) {
	$ssh_keypair_uuid=$data['0'];
	$description=$data['1'];
		echo ("<option value='$ssh_keypair_uuid'>$description</option>");
}

?>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Security Group
		<td align=center><select name=security_group_uuid>
<?
$query="select uuid,rule_name from security_group where account='$loguser'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
while ($data=@mysql_fetch_row($result)) {
	$security_group_uuid=$data['0'];
	$rule_name=$data['1'];
		echo ("<option value='$security_group_uuid'>$rule_name</option>");
}
?>
</table>
<p>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</form>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : Create SSH Keypair-->
<p><div id="ssh_create_key" title="Create New SSH-Keypair">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<form name=add_ssh_keypair method=post action="<?=$g4[bbs_path]?>/proc-add_ssh_keypair.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Description
		<td align=center><input type=text name=description size=30 maxlength=30>
</table>
<p>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</form>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : 물리 Host 추가 -->
<p><div id="add_physical_host" title="Add New Physical Host">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<form name=add_new_node method=post action="<?=$g4[bbs_path]?>/proc-add_node.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>IP Address
		<td align=center><input type=text name=ip_address size=30 maxlength=15>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>root's Password
		<td align=center><input type=text name=root_password size=30 maxlength=30>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Hypervisor
		<td align=center><select name=hypervisor>
			<option value=kvm>KVM</option>
			<option value=xen>XEN</option>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : 물리 Host 추가 -->
<p><div id="add_template" title="Add New Template Image">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<form name=add_template method=post action="<?=$g4[bbs_path]?>/proc-add_template.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Name
		<td align=center><input type=text name=name size=50 maxlength=100>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Description
		<td align=center><input type=text name=description size=50 maxlength=100>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Public
		<td align=center><input type=checkbox name=chk_public value=1 checked>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Featured
		<td align=center><input type=checkbox name=chk_featured value=1 checked>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Hypervisor
		<td align=center><select name=chk_hypervisor>
			<option>kvm</option>
			<!-- <option>xen</option> -->
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Bits
		<td align=center><select name=chk_bits>
			<option>64</option>
			<option>32</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>OS Type
		<td align=center><select name=chk_os_type>
			<option>RedHat</option>
			<option>Debian</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>URL
		<td align=center><input type=text name=url size=50 maxlength=200>
	<tr align=center>
		<td align=center bgcolor=99CCFF checked><b>Bootable
		<td align=center><input type=checkbox name=chk_bootable value=1 checked>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : 물리 Host 추가 -->
<p><div id="add_iso" title="Add New ISO Image">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<form name=add_iso method=post action="<?=$g4[bbs_path]?>/proc-add_iso.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Name
		<td align=center><input type=text name=name size=50 maxlength=100>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>Description
		<td align=center><input type=text name=description size=50 maxlength=100>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>OS Type
		<td align=center><select name=chk_os_type>
			<option>RedHat</option>
			<option>Debian</option>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>URL
		<td align=center><input type=text name=url size=50 maxlength=200>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : View Primary Storage -->
<p><div id="view_storage_primary" title="View Detail Info: Primary Storage">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<?
$query="select * from primary_storage";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
$data=@mysql_fetch_row($result);
	$uuid=$data[0];
	$host=$data[1];
	$fs_type=$data[2];
	$export_path=$data[3];
	$mount_path=$data[4];
	$create_time=$data[5];
	$update_time=$data[6];
	$total=$data[7];
	$used=$data[8];
	$free=$data[9];
?>
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Host
		<td align=center><?=$host?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>UUID
		<td align=center><?=$uuid?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>FS Type
		<td align=center><?=strtoupper($fs_type)?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Export Path
		<td align=center><?=$export_path?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Mount Path
		<td align=center><?=$mount_path?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Created
		<td align=center><?=date("Y/m/d H:i:s",$create_time)?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Updated
		<td align=center><?=date("Y/m/d H:i:s",$update_time)?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Total Size
		<td align=center><?=$total?> GB
	<tr align=center>
		<td align=center bgcolor=99CCFF>Used Size
		<td align=center><?=$used?> GB
	<tr align=center>
		<td align=center bgcolor=99CCFF>Free Size
		<td align=center><?=$free?> GB
	<tr align=center>
		<td align=center bgcolor=99CCFF>Usage
		<td align=center><?=round($used/$total*100,2)?> %
</table>
<p>
<input style='width: 110' type="button" onClick="javascript:goPage('proc-remove_primary_storage.php?uuid=<?=$uuid?>&mount_path=<?=$mount_path?>')" value="Remove"><br>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : Add Primary Storage -->
<p><div id="add_storage_primary" title="Register Primary Storage">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<?
$query="select count(*) from primary_storage";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

$data=mysql_fetch_row($result);
$chk_count=$data[0];

if ($chk_count != 0) {
?>
	<p><p>
	<font color=red><b>[Error] 이미 Primary Storage가 등록되어 있습니다.</b></font>
<?
} else {
?>
<form name=add_primary_storage method=post action="<?=$g4[bbs_path]?>/proc-add_primary_storage.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Host
		<td align=center><input type=text name=host size=30 maxlength=50>
	<tr align=center>
		<td align=center bgcolor=99CCFF>FS Type
		<td align=center><select name=fs_type>
			<option value=nfs>NFS
			<option value=glusterfs>GlusterFS
	<tr align=center>
		<td align=center bgcolor=99CCFF>Export Path
		<td align=center><input type=text name=export_path size=30 maxlength=50>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>
<?
}
?>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : View Secondary Storage -->
<p><div id="view_storage_secondary" title="View Detail Info: Primary Storage">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<?
$query="select * from secondary_storage";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
$data=@mysql_fetch_row($result);
	$uuid=$data[0];
	$host=$data[1];
	$fs_type=$data[2];
	$export_path=$data[3];
	$mount_path=$data[4];
	$create_time=$data[5];
	$update_time=$data[6];
	$total=$data[7];
	$used=$data[8];
	$free=$data[9];
?>
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Host
		<td align=center><?=$host?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>UUID
		<td align=center><?=$uuid?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>FS Type
		<td align=center><?=strtoupper($fs_type)?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Export Path
		<td align=center><?=$export_path?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Mount Path
		<td align=center><?=$mount_path?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Created
		<td align=center><?=date("Y/m/d H:i:s",$create_time)?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Updated
		<td align=center><?=date("Y/m/d H:i:s",$update_time)?>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Total Size
		<td align=center><?=$total?> GB
	<tr align=center>
		<td align=center bgcolor=99CCFF>Used Size
		<td align=center><?=$used?> GB
	<tr align=center>
		<td align=center bgcolor=99CCFF>Free Size
		<td align=center><?=$free?> GB
	<tr align=center>
		<td align=center bgcolor=99CCFF>Usage
		<td align=center><?=round($used/$total*100,2)?> %
</table>
<p>
<input style='width: 110' type="button" onClick="javascript:goPage('proc-remove_secondary_storage.php?uuid=<?=$uuid?>&mount_path=<?=$mount_path?>')" value="Remove"><br>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : Add Secondary Storage -->
<p><div id="add_storage_secondary" title="Register Secondary Storage">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<?
$query="select count(*) from secondary_storage";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

$data=mysql_fetch_row($result);
$chk_count=$data[0];

if ($chk_count != 0) {
?>
	<p><p>
	<font color=red><b>[Error] 이미 Secondary Storage가 등록되어 있습니다.</b></font>
<?
} else {
?>
<form name=add_secondary_storage method=post action="<?=$g4[bbs_path]?>/proc-add_secondary_storage.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Host
		<td align=center><input type=text name=host size=30 maxlength=50>
	<tr align=center>
		<td align=center bgcolor=99CCFF>FS Type
		<td align=center><select name=fs_type>
			<option value=nfs>NFS
			<option value=glusterfs>GlusterFS
	<tr align=center>
		<td align=center bgcolor=99CCFF>Export Path
		<td align=center><input type=text name=export_path size=30 maxlength=50>
	<tr align=center>
		<td align=center colspan=2>
			<input type=submit name=btn1 value="Submit">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type=reset name=btn2 value="Reset">
</table>
</form>
<?
}
?>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : View Network Pool -->
<p><div id="view_network_pool" title="Register Network Pool">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<?
$query="select * from network_pool";
#$query="select * from network_pool where used='1'";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
?>
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
	<tr align=center>
		<td align=center bgcolor=99CCFF><b>IP Address
		<td align=center bgcolor=99CCFF><b>Used
		<td align=center bgcolor=99CCFF><b>VM UUID
		<td align=center bgcolor=99CCFF><b>Account
		<td align=center bgcolor=99CCFF><b>Reset
	</tr>

<?
while ($data=@mysql_fetch_row($result)) {
	$ip_address=$data['0'];
	$used=$data['1'];
	$vm=$data['2'];
	$account=$data['3'];
?>
	<tr align=center>
		<td><?=$ip_address?>
		<td><?=$used?>
		<td><?=$vm?>
		<td><?=$account?>
<?
		if ($used != "0") {
		 	echo("<td><a href=proc-reset_network_pool.php?ip_address=$ip_address><font color=red>Reset</font></a>");
		} else {
			echo("<td>");
		}
?>
	</tr>
<?
}
?>
</table>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : Add Network Pool -->
<p><div id="add_network_pool" title="Register Network Pool">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<?
$query="select count(*) from network_pool";
$result=@mysql_query($query);
if(!$result) {
    Query_Error();
}

$data=mysql_fetch_row($result);
if($data['0']!=0) {
?>
	<p><p>
    <font color=red><b>[Error] 이미 Network Pool이 등록되어 있습니다.</b></font>
<?
} else {
?>
<form name=add_network_pool method=post action="<?=$g4[bbs_path]?>/proc-add_network_pool.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
    <tr align=center>
        <td align=center bgcolor=99CCFF>Begin
        <td align=center><input type=text name=begin size=30 maxlength=60>
    <tr align=center>
        <td align=center bgcolor=99CCFF>End
        <td align=center><input type=text name=end size=30 maxlength=60>
    <tr align=center>
        <td align=center colspan=2>
            <input type=submit name=btn1 value="Submit">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type=reset name=btn2 value="Reset">
</table>
</form>
<?
}
?>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 내용들 시작 : Add Security Group -->
<p><div id="add_security_group" title="Create Security Group">
<?
$loguser=@$member['mb_id'];
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);
?>
<form name=add_security_group method=post action="<?=$g4[bbs_path]?>/proc-add_security_group.php">
<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
    <tr align=center>
        <td align=center bgcolor=99CCFF>Rule Name
        <td align=center><input type=text name=rule_name size=30 maxlength=60>
    <tr align=center>
        <td align=center bgcolor=99CCFF>Description
        <td align=center><input type=text name=description size=30 maxlength=60>
    <tr align=center>
        <td align=center colspan=2>
            <input type=submit name=btn1 value="Submit">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type=reset name=btn2 value="Reset">
</table>
</form>
<?
mysql_close();
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password = '1234';
$mysql_db = 'board';
mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_select_db($mysql_db);
?>
</div>

<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->
<!-- Dialog 전체 끝 -->

<p>

                <?
                $sql = "select * from $g4[group_table] ";
                if ($gr_id)
                    $sql .= " where gr_id = '$gr_id' ";
                $sql.= " order by gr_1 ";

                $qry = sql_query($sql);
                for ($i=0; $row=sql_fetch_array($qry); $i++) {
                ?>
                    <div class="sm_item">
                    <div class="sm_title"><a href="<?=$g4[bbs_path]?>/group.php?gr_id=<?=$row[gr_id]?>"><?=$row[gr_subject]?></a></div>
                    <?
                    $sql2 = "select * from $g4[board_table] where gr_id = '$row[gr_id]' order by bo_order_search asc";
                    $qry2 = sql_query($sql2);
                    for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
                        if ($row2[bo_table] == $bo_table) $class = "sm_sub selected"; else $class = "sm_sub";
                    ?>
                        <div class="<?=$class?>"><a href="<?=$g4[bbs_path]?>/board.php?bo_table=<?=$row2[bo_table]?>"><?=$row2[bo_subject]?></a></div>
                    <? } ?>
                    </div> <!-- sm_item -->
                <? } ?>
            </div> <!-- sm_margin -->
            </div> <!-- sm_border -->

        </div> <!-- sm -->
        <div class="poll"><?=poll("mw.poll")?></div>
    </td>
    <td width="10"></td>
    <td valign="top">

