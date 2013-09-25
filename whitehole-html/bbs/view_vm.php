<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";
?>
<!doctype html>
<html>
<head>
	<title>Whitehole - Node List</title>
<!--
	<link rel="stylesheet" href="style01.css" type="text/css">
	<script language='javascript'> 
		window.setTimeout('window.location.reload()',60000);
	</script>
-->
<!--
	<link rel="stylesheet" href="../jquery/css/jquery-ui.css" />
	<script src="../jquery/js/jquery-1.9.1.js"></script>
	<script src="../jquery/js/jquery-ui.js"></script>
-->
	<link rel="stylesheet" href="../jquery/css/cupertino/jquery-ui-1.10.3.custom.css" />
	<script src="../jquery/js/jquery-1.9.1.js"></script>
	<script src="../jquery/js/jquery-ui-1.10.3.custom.js"></script>
<?
$pre_order=$_GET['order'];
if(!$pre_order) {
	$pre_order="0";
}
?>
<script>
  $(function() {
    $("#list").accordion({
      active: <?=$pre_order?>,
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

  $(function() {
    $( "#dialog_notice" ).dialog({
      position: ['middle',100]
    });
  });
</script>


</head>
<body>

<!-- 새창 열기 javascript 함수 -->
<script type="text/javascript">
function goPage(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=950, height=700";
   window.open(url, name, winFeatures); 
}
function goPageMrtg(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=1070, height=700";
   window.open(url, name, winFeatures); 
}
function goPageEditSnapshot(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=800, height=700"
   window.open(url, name, winFeatures); 
}
function goPageEditSG(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=1100, height=700";
   window.open(url, name, winFeatures); 
}
function goPageMig(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=600, height=200";
   window.open(url, name, winFeatures); 
}
</script>

<?
$pre_create=$_GET['pre_create'];
if (isset($pre_create)) {
    if ($_GET['pre_create']=="yes") {
		$pre_vm_uuid=$_GET['pre_vm_uuid'];
		$pre_vm_name=$_GET['pre_vm_name'];
		$pre_target_node=$_GET['pre_target_node'];
		$pre_vnc_port=$_GET['pre_vnc_port'];
?>
        <script type="text/javascript">
            goPage('<?=$pre_vm_uuid?>','vncviewer.php?name=<?=$pre_vm_name?>&node=<?=$pre_target_node?>&vnc_port=<?=$pre_vnc_port?>&uuid=<?=$pre_vm_uuid?>')
        </script>
		<script language="javascript">
			location.href="view_vm.php?order=<?=$pre_order?>"
		</script>
<?
    }
}
?>

<?
if ($loguser=="admin") {
	$query="select * from info_vm order by create_time desc";
} else {
	$query="select * from info_vm where account='$loguser' order by create_time desc";
}

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
$num_row=mysql_num_rows($result);
?>

<? if ($num_row==0) { ?>
<div id="dialog_notice" title="Notice">
  <p>
	생성된 VM(인스턴스)가 없습니다.<p>
	좌측 메뉴를 사용해서 VM을 먼저 생성 하세요.
  </p>
</div>
<? } else { ?>
<div id="list" style='width: 99%'>

<?
$order=0;
while ($data = mysql_fetch_row($result)) {
	$uuid = $data['0'];
	$create_time = date("Y/m/d H:i:s",$data['1']);
	$sshkey_uuid = $data['2'];
	$sshkey_desc = $data['3'];
	$ip_address = $data['4'];
	$name = $data['5'];
	$cpu = $data['6'];
	$memory = $data['7'];
	$memory_h = $data['7']/1024;
	$mac = $data['8'];
	$bits = $data['9'];
	$hypervisor = $data['10'];
	$node = $data['11'];
	$vnc_port = $data['12'];
	$account = $data['13'];
	$data_volume = $data['14'];
	$os_type = $data['15'];
	$hostname = $data['16'];
	$status = $data['17'];
	$security_group_uuid = $data['18'];
	$origin = explode("___", $data['19']);
	$origin_type = $origin['0'];
	$origin_uuid = $origin['1'];
#	$diff_time = time()-$update_time;

	$query_sg="select rule_name,uuid from security_group where uuid='$security_group_uuid'";
	$result_sg=@mysql_query($query_sg);
	if (!$result_sg) {
	    Query_Error();
	}
	$data_sg=@mysql_fetch_row($result_sg);
	$security_group_rule_name=$data_sg['0'];
	$security_group_uuid=$data_sg['1'];
	$protect = $data['20'];
?>
	<h3>
		<table width="98%" border=0>
<? if ($loguser=="admin") {
?>
		<tr>
			<td width="100" align="center">
				<?
				if ($status==1) { echo ("<font color=blue><b>&nbspRunning&nbsp</b></font>"); } else { echo ("<font color=red><b>&nbspUnknown&nbsp</b></font>"); };
				?>
			</td>
			<td width=300 align="left">
				<?
				echo "<b>$name</b>";
				?>
			</td>
			<td align="left">
				<?
				echo "<b>Owner: $account</b>";
				?>
			</td>
			<td align="right">
				<?
				echo "<b>(UUID: $uuid)</b>";
				?>
			</td>
		</tr>
<?
} else {
?>
		<tr>
			<td width="100" align="center">
				<?
				if ($status==1) { echo ("<font color=blue><b>&nbspRunning&nbsp</b></font>"); } else { echo ("<font color=red><b>&nbspUnknown&nbsp</b></font>"); };
				?>
			</td>
			<td width="300" align="left">
				<?
				echo "<b>$name</b>";
				?>
			</td>
			<td align="right">
				<?
				echo "<b>(UUID: $uuid)</b>";
				?>
			</td>
		</tr>
<?
}
?>
		</table>
	</h3>
		<div>
			<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
				<tr bgcolor="efffff">
					<? if ($loguser=="admin") { ?>
					<td align="center" width=290><b>Admin
					<? } ?>
					<td align="center"><b>VM Control
					<td align="center" width=180><b>Resource
					<td align="center"><b>Networking
					<td align="center"><b>Etc
					<td align="center"><b>Terminate
				<tr>
				<? if ($loguser=="admin") { ?>
					<td>
						<ul>
						<li><b>VNC Port:</b> <?=$vnc_port?><p></li>
						<li><b>OS Type:</b> <?=$os_type?> (<?=$bits?> bit)<p></li>
						<li><b>Hypervisor:</b> <?=$hypervisor?><p></li>
						<li><b>Host Node:</b> <?=$node?><p></li>
						<li><b>Origin:</b>
<?
if ($origin_type=="T") {
	echo "Template";
	$query_origin="select name from vm_template where uuid='$origin_uuid'";
} else if ($origin_type=="I") {
	$query_origin="select name from iso where uuid='$origin_uuid'";
	echo "ISO";
}
if($query_origin) {
    $result_origin=@mysql_query($query_origin);
    if (!$result_origin) {
        Query_Error();
    }
    $data_origin=@mysql_fetch_row($result_origin);
	$origin_name=$data_origin['0'];
	#echo ("<br>($origin_uuid)");
	echo ("<br>($origin_name)");
}
?>
						<p></li>
						</ul>
					</td>
				<? } ?>
					<td align="center" width=150>
						<table align=center width=95%>
							<tr align=center>
								<td><input style='width: 117' type="button" onClick="javascript:goPage('<?=$uuid?>','vncviewer.php?name=<?=$name?>&node=<?=$node?>&vnc_port=<?=$vnc_port?>&uuid=<?=$uuid?>')" value="View Colsole"><br>
								<? if ($loguser=="admin") { ?>
							</tr>
							<tr align=center>
								<td><input style='width: 117' type="button" onClick="javascript:goPageMig('Mig-<?=$uuid?>','migrate_live.php?vm_ip=<?=$ip_address?>&vm_name=<?=$name?>&src_host_ip=<?=$node?>&cpu=<?=$cpu?>&mem=<?=$memory_h?>&vm_uuid=<?=$uuid?>&order=<?=$order?>')" value="Live Migration"><br>
								<? } ?>
								<?
								if($status==1) {
								?>
							</tr>
							<tr align=center>
								<td>
									<input style="width: 30" type="button" onClick="location.href='#'" value='On' disabled="true">
									<input style="width: 30" type="button" onClick="location.href='proc-power_off.php?node=<?=$node?>&name=<?=$name?>&uuid=<?=$uuid?>&order=<?=$order?>'" value='Off'>
									<input style="width: 50" type="button" onClick="location.href='proc-power_reset.php?node=<?=$node?>&name=<?=$name?>&uuid=<?=$uuid?>&order=<?=$order?>'" value='Reset'>
								<?
								} else {
								?>
							</tr>
							<tr align=center>
								<td>
									<input style="width: 30" type="button" onClick="location.href='proc-power_on.php?node=<?=$node?>&name=<?=$name?>&uuid=<?=$uuid?>&order=<?=$order?>'" value='On'>
									<input style="width: 30" type="button" onClick="location.href='#'" value='Off' disabled="true">
									<input style="width: 50" type="button" onClick="location.href='#'" value='Reset' disabled="true">
								<?
								}
								?>
							</tr>
							<tr align=center>
								<td>
								<input style='width: 117' type="button" onClick="javascript:goPageEditSnapshot('Snapshot-<?=$uuid?>','edit_snapshots.php?vm_uuid=<?=$uuid?>&vm_name=<?=$name?>&target_node=<?=$node?>&vnc_port=<?=$vnc_port?>')" value="Snapshot"><br>
							</tr>
							<tr align=center>
								<td>
								<input style='width: 117' type="button" onClick="javascript:goPageEditSG('EditSG-<?="$uuid"?>','edit_security_group.php?vm_uuid=<?=$uuid?>&rule_name=<?=$security_group_rule_name?>&uuid=<?=$security_group_uuid?>&account=<?=$account?>')" value="Security Group"><br>
							</tr>
						</table>
					</td>
					<td>
						<center><input style="width: 110" type="button" onClick="javascript:goPageMrtg('Mon-<?=$uuid?>','http://@_LOCAL_IP_@/mrtg/<?=$uuid?>')" value='Monitoring'></center>
						<ul>
						<li><b>vCPU:</b> <?=$cpu?> EA<p></li>
						<li><b>vMEM:</b> <?=$memory_h?> MB<p></li>
						<li><b>2nd-Volume:</b>	<? if (!$data_volume) { echo ("N/A"); } else { echo ("$data_volume GB"); } ?><p></li>
						</ul>
					</td>
					<td>
						<ul>
						<li><b>IP Addres:</b> <?=$ip_address?><p></li>
						<li><b>Mac Addres:</b> <?=$mac?><p></li>
						<li><b>Netmask:</b> @_NETMASK_@<p></li>
						<li><b>Gateway:</b> @_GATEWAY_@<p></li>
					</td>
					<td>
						<ul>
						<li><b>Security Group:</b> <?=$security_group_rule_name?><p></li>
						<li><b>SSH-Key:</b> <?=$sshkey_desc?><p></li>
						<li><b>Created:</b> <?=$create_time?><p></li>
						</ul>
					<td align="center">
						<font color=blue><b>Protection:</b>&nbsp;<b><?if ($protect=="1") { echo "<font color=ff00ff>ON</font>"; } else { echo"<font color=b22222>OFF</font>"; }?></b><p>
						<input style="width: 30" type="button" onClick="location.href='proc-protect-ctl.php?uuid=<?=$uuid?>&job=on&order=<?=$order?>'" value='On' <? if($protect=="1") { echo ("disabled='true'"); }?>>
						<input style="width: 30" type="button" onClick="location.href='proc-protect-ctl.php?uuid=<?=$uuid?>&job=off&order=<?=$order?>'" value='Off' <? if($protect=="0") { echo ("disabled='true'"); }?>><p>
						<font color=red><b>WARNING!!</b></font><p>
						<font color=red><b>Lost All Data !!</b></font><p>
						<input style="width: 110" type="button" onClick="location.href='proc-remove_vm.php?uuid=<?=$uuid?>&name=<?=$name?>&hypervisor=<?=$hypervisor?>&node=<?=$node?>&sshkey_uuid=<?=$sshkey_uuid?>&ip_addrss=<?=$ip_address?>'" value='Terminate'><p>
					</td>
				</tr>
			</table>
		</div>
<?
	$order=$order+1;
}
?>
</div>

<div style="float:right;"><a onClick="accordionDestroy()">Expand all</a></div>

<?
}
?>

</body>
</html>
<? include_once("./_tail.whitehole.php"); ?>
