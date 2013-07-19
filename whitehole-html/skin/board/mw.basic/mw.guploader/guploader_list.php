<?
include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if (!trim($bo_table)) exit;
if (!trim($wr_id)) $wr_id = 0;

$json = "{\"files\":[";

if ($wr_id && $w == 'u') {
    if ($write[mb_id] != $member[mb_id] && !$is_admin) exit;
    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' order by bf_no";
    $qry = sql_query($sql);
} else {
    $sql = "select * from $mw[guploader_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and bf_ip = '$_SERVER[REMOTE_ADDR]' order by bf_no";
    $qry = sql_query($sql, false);
}
for ($i=0; $row=sql_fetch_array($qry); $i++) {
    if ($i>0) $json .= ",";
    $json .= "{\"id\":\"{$row[wr_id]}\",\"file_num\":\"{$row[bf_no]}\",\"save_name\":\"{$row[bf_file]}\",";
    $json .= "\"real_name\":\"{$row[bf_source]}\", \"file_size\":\"{$row[bf_filesize]}\"}";
}
$json .= "]}";

echo $json;
?>
