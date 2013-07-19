<?
include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if ($wr_id) {
	$sql = " select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' order by bf_no desc limit $bf_position ";
	$row = sql_fetch($sql);
	echo "{\"bf_no\":\"{$row[bf_no]}\", \"bf_source\":\"{$row[bf_source]}\", \"bf_file\":\"{$row[bf_file]}\", \"bf_filesize\":\"{$row[bf_filesize]}\", \"bf_width\":\"{$row[bf_width]}\", \"bf_type\":\"{$row[bf_type]}\"}";
} else {
	$sql = " select * from $mw[guploader_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and bf_ip = '$_SERVER[REMOTE_ADDR]' order by bf_no desc limit $bf_position ";
	$row = sql_fetch($sql);
	echo "{\"bf_no\":\"{$row[bf_no]}\", \"bf_source\":\"{$row[bf_source]}\", \"bf_file\":\"{$row[bf_file]}\", \"bf_filesize\":\"{$row[bf_filesize]}\", \"bf_width\":\"{$row[bf_width]}\", \"bf_type\":\"{$row[bf_type]}\"}";
}
?>