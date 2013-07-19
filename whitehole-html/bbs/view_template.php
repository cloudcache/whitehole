<? include_once("./_head.whitehole.php"); ?>
<html>
<head>
	<title>Whitehole : Status Node</title>
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
include "../db_conn.php";
include "../functions.php";

$mount_path="/home/mnt/sec";

$query="select * from vm_template";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}

?>

<div id="list" style='width: 99%'>

<?
while ($data=@mysql_fetch_row($result)) {
#print_r($data);
	$uuid=$data[0];
	$name=$data[1];
	$public=$data[2];
	$featured=$data[3];
	$hypervisor=$data[4];
	$bits=$data[5];
	$url=$data[6];
	$format=$data[7];
	$create_time=$data[8];
	$account=$data[9];
	$description=$data[10];
	$bootable=$data[11];
	$size_virtual=$data[12];
	$size_real=$data[13];
	$size_verify=$data[14];
	$os_type=$data[15];
?>

    <h3>
        <table width=98%>
            <tr>
                <td width=500>
					<table>
						<tr>
							<td width=130><b>Owner: <?=$account?></b>
							<td><font color=blue><b><?=$name?></b></font>
						</tr>
					</table>
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
                    <td align="center"><b>Public<b>
                    <td align="center"><b>HVM<b>
                    <td align="center"><b>OS Type<b>
                    <td align="center"><b>Format<b>
                    <td align="center"><b>Created<b>
                    <td align="center"><b>Bootable<b>
                    <td align="center"><b>Size (Virtual)<b>
                    <td align="center"><b>Size (Real)<b>
                    <td align="center"><b>Terminate<b>
                </tr>
                <tr>
                    <td align="center">
						<?if ($public==1) { echo ("Yes"); } else { echo ("No"); }?>
                    </td>
                    <td align="center">
						<?if ($hypervisor==kvm) { echo ("Yes"); } else { echo ("No"); }?>
                    </td>
                    <td align="center">
						<?=$os_type?><br>(<?=$bits?>bit)
                    </td>
                    <td align="center">
						<?=$format?>
                    </td>
                    <td align="center">
						<?=date("Y/m/d H:i:s",$create_time)?>
                    </td>
                    <td align="center">
						<?if ($bootable==1) { echo ("Yes"); } else { echo ("No"); }?>
                    </td>
                    <td align="center">
						<?=$size_virtual?> GB
                    </td>
                    <td align="center">
						<?=$size_real?> GB
                    </td>
                    <td align="center">
                        <input style='width: 110' type="button" onClick="location.href='proc-remove_template.php?uuid=<?=$uuid?>'" value="Terminate"><br>
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
