<?php
/**
 * Bechu-Basic Skin for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

@ini_set('memory_limit', '-1');
@set_time_limit(0);

if ($is_admin != "super") 
    alert_close("최고관리자만 접근 가능합니다.");

if (!strstr($_SERVER[HTTP_REFERER], "mw.proc/mw.intercept.php"))
    alert_close("잘못된 접근입니다.");

$token = md5(session_id().$member[mb_today_login].$member[mb_login_ip]);
if (($token != get_session("ss_token")) || ($token != $form_token))
    alert_close("잘못된 접근입니다.");

$mb = get_member($mb_id);

$mb_intercept_date = date("Ymd", $g4[server_time]);
sql_query("update $g4[member_table] set mb_level = '1', mb_intercept_date = '$mb_intercept_date', mb_memo = '$mb_memo' where mb_id='$mb_id'");

if ($is_all_delete or $is_all_move) {
    $all_board_sql = "select * from $g4[board_table] ";
    $all_board_qry = sql_query($all_board_sql);
    while ($all_board_row = sql_fetch_array($all_board_qry)) {
        $all_write_sql = "select * from $g4[write_prefix]$all_board_row[bo_table] where mb_id = '$mb_id' order by wr_id, wr_is_comment desc";
        $all_write_qry = sql_query($all_write_sql);
        while ($all_write_row = sql_fetch_array($all_write_qry)) {
            if ($is_all_delete or $all_write_row[wr_is_comment])
                mw_delete_row($all_board_row, $all_write_row);
            elseif ($is_all_move)
                mw_move($all_write_row[wr_id], $move_table, 'move');
        } // write row
    } // board row
}

alert_close("$mb[mb_nick] 회원을 접근차단하였습니다.");

