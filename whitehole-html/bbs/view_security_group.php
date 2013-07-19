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

  $(function() {
    $( "#dialog_edit" ).dialog({
      position: ['middle',100]
    });
  });
</script>


</head>
<body>

<!-- 새창 열기 javascript 함수 -->
<script type="text/javascript">
function goPage(name, url){
   var winFeatures = "toolbar=no," + "location=no," + "directories=no," + "status=yes," + "menubar=yes," +  "scrollbars=yes," + "resizable=yes," + "width=1100, height=700";
   window.open(url, name, winFeatures); 
}
</script>

<?
if ($loguser=="admin") {
	$query="select * from security_group order by create_time desc";
} else {
	$query="select * from security_group where account='$loguser' order by create_time desc";
}

$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
$num_row=mysql_num_rows($result);
?>

<? if ($num_row==0) { ?>
<div id="dialog_edit" title="Notice">
  <p>
	등록된 Security Group 이 없습니다.<p>
	좌측 메뉴를 사용해서 VM을 먼저 생성 하세요.
  </p>
</div>
<? } else { ?>
<div id="list" style='width: 99%'>

<?
while ($data = mysql_fetch_row($result)) {
	$uuid = $data['0'];
	$rule_name = $data['1'];
	$account = $data['2'];
	$description = $data['3'];
	$used_count = $data['4'];
	$create_time = date("Y/m/d H:i:s",$data['5']);
?>
	<h3>
        <table width=98%>
            <tr>
                <? if ($loguser=="admin") { ?>
                <td width=200>
                    <b>(Owner: <?=$account?>)
                </td>
                <td>
                    <b>Rule-Name: <font color=blue><?=$rule_name?></font></b>
                </td>
                <td align="right">
                    <b>(UUID: <?=$uuid?>)</b>
                </td>
                <? } else { ?>
                <td width=200>
                    <b>Rule-Name: <font color=blue><?=$rule_name?></font></b>
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
					<td align="center"><b>Description
					<td align="center"><b>Created
					<td align="center"><b>Used Count
					<td align="center"><b>Edit
					<td align="center"><b>Remove
				<tr>
					<td align="center">
						<?=$description?>
					</td>
					<td align="center">
						<?=$create_time?>
					</td>
					<td align="center">
						<?=$used_count?>
					</td>
					<td align="center" width=120>
						<input style='width: 110' type="button" onClick="javascript:goPage('<? echo "$uuid"?>','edit_security_group.php?rule_name=<?=$rule_name?>&uuid=<?=$uuid?>&account=<?=$account?>')" value="Edit"><br>
					</td>
					<td align="center" width=120>
						<? if ($used_count==0) { ?>
							<input style='width: 110' type="button" onClick="location.href='proc-remove_security_group.php?rule_name=<?=$rule_name?>&uuid=<?=$uuid?>&account=<?=$account?>'" value="Remove"><br>
						<? } else { ?>
							<input style='width: 110' type="button" value="Remove" disabled=true><br>
						<? } ?>
					</td>
				</tr>
			</table>
		</div>
<?
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
