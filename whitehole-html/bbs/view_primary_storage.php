<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";
include "../check_admin.php";

//$ssh_host = "192.168.100.122";

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

<html>
<head>
	<title>Whitehole : Status Node</title>
</head>
<body>


<table align=center width=500 border=1 cellspacing=0 cellpadding=5>
	<tr align=center>
		<td align=center bgcolor=99CCFF>Host
		<td align=center><?=$host?>
		<a href='proc-remove_primary_storage.php?uuid=<?=$uuid?>&mount_path=<?=$mount_path?>'><?if($uuid) {?><font color=red>Remove</font><?}?></a>
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
<? include_once("./_tail.whitehole.php"); ?>
