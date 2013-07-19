<?
include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if (!trim($bo_table)) exit;
if (!trim($wr_id)) $wr_id = 0;
 
if ($wr_id && $w == 'u')
{
    if ($write[mb_id] != $member[mb_id] && !$is_admin) exit;

    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$bf_no'";
    $row = sql_fetch($sql);

    @unlink("$g4[path]/data/file/$bo_table/$row[bf_file]");
    @unlink("$g4[path]/data/file/$bo_table/thumbnail/$wr_id");
    @unlink("$g4[path]/data/file/$bo_table/thumbnail2/$wr_id");
    @unlink("$g4[path]/data/file/$bo_table/thumbnail3/$wr_id");

    $sql = "delete from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$bf_no'";
    $qry = sql_query($sql);
}
else
{
    $sql = "select * from $mw[guploader_table] where bo_table = '$bo_table' and bf_no = '$bf_no' and mb_id = '$member[mb_id]' and bf_ip = '$_SERVER[REMOTE_ADDR]'";
    $row = sql_fetch($sql);

    @unlink("$g4[path]/data/guploader/$row[bf_file]");

    $sql = "delete from $mw[guploader_table] where bo_table = '$bo_table' and bf_no = '$bf_no' and mb_id = '$member[mb_id]' and bf_ip = '$_SERVER[REMOTE_ADDR]'";
    $qry = sql_query($sql);
}
?>
