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

$query="select * from iso";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
?>

<div id="list" style='width: 99%'>
<?
while ($data=@mysql_fetch_row($result)) {
	$uuid=$data[0];
	$create_time=$data[1];
	$name=$data[2];
	$description=$data[3];
	$os_type=$data[4];
	$url=$data[5];
?>
    <h3>
        <table width=98%>
            <tr>
                <td width=300>
                    <font color=blue><b><?=$name?></b></font>
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
                    <td align="center"><b>Description<b>
                    <td align="center"><b>UUID<b>
                    <td align="center"><b>OS Type<b>
                    <td align="center"><b>Create Time<b>
                    <td align="center"><b>Terminate<b>
                </tr>
                <tr>
                    <td align="center">
                        <?=$description?>
                    </td>
                    <td align="center">
						<?=$uuid?>
                    </td>
                    <td align="center">
                        <?=$os_type?>
                    </td>
                    <td align="center">
                        <?=$create_time?>
                    </td>
                    <td align="center">
						<input style='width: 110' type="button" onClick="javascript:goPage('proc-remove_iso.php?uuid=<?=$uuid?>')" value="Remove"><br>
                    </td>
                </tr>
            </table>
        </div>
<?
}
?>

</div>
<div style="float:right;"><a onClick="accordionDestroy()">Expand all</a></div>

<? include_once("./_tail.whitehole.php"); ?>
