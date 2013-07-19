<?
include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

$html_title = "$group[gr_subject] > $board[bo_subject] > " . conv_subject($write[wr_subject], 255) . " > 링크";

if (!($bo_table && $wr_id && $no)) 
    alert_close("값이 제대로 넘어오지 않았습니다.");

// SQL Injection 예방
$row = sql_fetch(" select count(*) as cnt from {$g4[write_prefix]}{$bo_table} ", FALSE);
if (!$row[cnt])
    alert_close("존재하는 게시판이 아닙니다.");

if (!$write["wr_link{$no}"])
    alert_close("링크가 없습니다.");

$ss_name = "ss_link_{$bo_table}_{$wr_id}_{$no}";
if (empty($_SESSION[$ss_name])) 
{
    $sql = " update {$g4[write_prefix]}{$bo_table} set wr_link{$no}_hit = wr_link{$no}_hit + 1 where wr_id = '$wr_id' ";
    sql_query($sql);

    set_session($ss_name, true);
}

if ($mw_basic[cf_link_log]) { // 링크 기록
    $ll_name = $board[bo_use_name] ? $member[mb_name] : $member[mb_nick];
    $sql = "insert into $mw[link_log_table]
               set bo_table = '$bo_table'
                   , wr_id = '$wr_id'
                   , ll_no = '$no'
                   , mb_id = '$member[mb_id]'
                   , ll_name = '$ll_name'
                   , ll_ip = '$_SERVER[REMOTE_ADDR]'
                   , ll_datetime = '$g4[time_ymdhis]'";
    $qry = sql_query($sql);
}

goto_url(set_http($write["wr_link{$no}"]));
?>
