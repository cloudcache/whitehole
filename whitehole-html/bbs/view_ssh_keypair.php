<? include_once("./_head.whitehole.php"); ?>
<html>
<head>
	<title>Whitehole - SSH KeyPair List</title>

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

$ssh_keypair_dir="/home/mnt/sec/ssh-keypair/$loguser";

if ($loguser=="admin") {
	$query="select * from ssh_keypair";
} else {
	$query="select * from ssh_keypair where account='$loguser'";
}
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
?>

<div id="list" style='width: 99%'>
<?
while ($data = mysql_fetch_row($result)) {
	$uuid = $data[0];
	$account = $data[1];
	$description = $data[2];
	$used_count = $data[3];
	$create_time = date("Y/m/d H:i:s",$data['4']);
?>
    <h3>
		<table width=98%>
			<tr>
				<? if ($loguser=="admin") { ?>
				<td width=200>
					<b>(Owner: <?=$account?>)
				</td>
				<td>
					<b>Key-Name: <font color=blue><?=$description?></font></b>
				</td>
				<td align="right">
					<b>(UUID: <?=$uuid?>)</b>
				</td>
				<? } else { ?>
				<td width=200>
					<b>Key-Name: <font color=blue><?=$description?></font></b>
				</td>
				<td align="right">
					<b>(UUID: <?=$uuid?>)</b>
				</td>
				<?
				}
				?>
			</tr>
		</table>
    </h3>
		<div>
			<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=lightblue>
				<tr bgcolor="efffff">
					<td align="center"><b>Description<b>
                    <td align="center"><b>UUID<b>
                    <td align="center"><b>Used Count<b>
                    <td align="center"><b>Create Time<b>
                    <td align="center"><b>Terminate<b>
				</tr>
				<tr>
					<td align="center" width=200>
						<?=$description?>
					</td>
					<td align="left" width=400>
						<table>
							<tr>
								<td align=center><b>Private-Key:</td>
								<td><a href="proc-download_ssh_key.php?key_path=<?=$ssh_keypair_dir?>&key_file=<?=$uuid?>"><font color="dodgerblue"><?=$uuid?></font></a></td>
							</tr>
							<tr>
								<td align=center><b>Public-Key:</td>
								<td><a href="proc-download_ssh_key.php?key_path=<?=$ssh_keypair_dir?>&key_file=<?=$uuid?>.pub"><font color="dodgerblue"><?=$uuid?>.pub</font></a></td>
							</tr>
						</table>
					</td>
					<td align="center">
						<?=$used_count?>
					</td>
					<td align="center">
						<?=$create_time?>
					</td>
					<td align="center">
						<input style='width: 110' type="button" onClick="location.href='proc-remove_ssh_keypair.php?uuid=<?=$uuid?>&used_count=<?=$used_count?>'" value="Terminate"><br>
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
