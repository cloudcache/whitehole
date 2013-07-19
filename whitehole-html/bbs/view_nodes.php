<?
include_once("./_head.whitehole.php");
include_once("./check_admin.php");
?>
<html>
<head>
	<title>Whitehole - Node List</title>
<!--
	<script language='javascript'> 
		window.setTimeout('window.location.reload()',60000);
	</script>
-->
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

<!-- 새창 열기 javascript 함수 -->
<script type="text/javascript">
function goPage(url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=330, height=350";
   window.open(url,'popup', winFeatures); 
}
</script>

<?
include "../db_conn.php";
include "../functions.php";

$query="select * from info_node";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
?>

<div id="list" style='width: 99%'>

<?
while ($data = mysql_fetch_row($result)) {
	$ip_address = $data[0];
	$hostname = $data[1];
	$create_time = $data[2];
	$update_time = $data[3];
	$status = $data[4];
	$total_sys_core = $data[5];
	$total_sys_mem = $data[6];
	$free_sys_cpu = $data[7];
	$free_sys_mem = $data[8];
	$total_vms = $data[9];
	$active_vms = $data[10];
	$inactive_vms = $data[11];
	$hypervisor = $data[12];
	$host_id = $data[13];
	$diff_time = time()-$update_time;
?>

    <h3>
        <table width=98%>
            <tr>
                <td width=200>
                    <font color=blue><b><?=$hostname?></b></font>
                </td>
                <td width=200>
                    <b>(IP: <?=$ip_address?>)</b>
                </td>
                <td align="right">
                    <b>(UUID: <?=$uuid?>)</b>
                </td>
            </tr>
        </table>
    </h3>

        <div>
            <table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
                <tr bgcolor="efffff">
                    <td align="center"><b>View<b>
                    <td align="center"><b>Createed<b>
                    <td align="center"><b>Last Updated<b>
                    <td align="center"><b>Status<b>
                    <td align="center"><b>Cores (EA)<b>
                    <td align="center"><b>Memory (MB)<b>
                    <td align="center"><b>Free CPU (%)<b>
                    <td align="center"><b>Free Memory (MB)<b>
                    <td align="center"><b>Total VMs<b>
                    <td align="center"><b>Active VMs<b>
                    <td align="center"><b>In-Active VMs<b>
                    <td align="center"><b>Remove Host<b>
                </tr>
                <tr>
                    <td align="center">
                        <input style='width: 80' type="button" onClick="javascript:goPage('detail_node.php?ip_address=<?=$ip_address?>&hostname=<?=$hostname?>')" value="Detail Info"><br>
                    </td>
                    <td align="center">
						<?=date("Y/m/d H:i:s",$create_time)?>
                    </td>
                    <td align="center">
						<?
							if ($diff_time<600) {
								echo (date('Y/m/d H:i:s',$update_time));
							} else {
								echo (date('Y/m/d H:i:s',$update_time));
							}
						?>
                    </td>
                    <td align="center">
						<?
							if ($status = 1) { echo ("<font color=blue><b>Running</b></font>"); } else { echo ("<font color=red><b>Unknown</b></font>"); }
						?>
                    </td>
                    <td align="center">
						<?=$total_sys_core?> EA
                    </td>
                    <td align="center">
						<?=$total_sys_mem?> MB
                    </td>
                    <td align="center">
						<?=$free_sys_cpu?> %
                    </td>
                    <td align="center">
						<?=$free_sys_mem?> MB
                    </td>
                    <td align="center">
						<?=$total_vms?> EA
                    </td>
                    <td align="center">
						<?=$active_vms?> EA
                    </td>
                    <td align="center">
						<?=$inactive_vms?> EA
                    </td>
                    <td align="center">
                        <input style='width: 110' type="button" onClick="location.href='proc-remove_node.php?ip_address=<?=$ip_address?>&total_vms=<?=$total_vms?>'" value="Remove"><br>
                    </td>
                </tr>
            </table>
        </div>
<?
}
?>
</div>

<div style="float:right;"><a onClick="accordionDestroy()">Expand all</a></div>



</body>
</html>
<? include_once("./_tail.whitehole.php"); ?>
