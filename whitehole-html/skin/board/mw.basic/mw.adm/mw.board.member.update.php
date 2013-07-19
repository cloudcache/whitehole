<?
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

if ($is_admin != "super")
    alert("접근 권한이 없습니다.");

if ($w == "")
{
    $sql = "select * from $mw[board_member_table] where bo_table = '$bo_table' and mb_id = '$mb_id'";
    $row = sql_fetch($sql);
    if ($row)
        alert("이미 등록된 회원ID 입니다.");

    $sql = "select * from $g4[member_table] where mb_id = '$mb_id'";
    $row = sql_fetch($sql);
    if (!$row)
        alert("존재하지 않는 회원ID 입니다.");

    $sql = "insert into $mw[board_member_table]
               set bo_table = '$bo_table'
                   ,mb_id = '$mb_id'
                   ,bm_datetime = '$g4[time_ymdhis]'";
    sql_query($sql);
    $msg = "등록 하였습니다.";
}
else if ($w == "d")
{
    $sql = "delete from $mw[board_member_table] where bo_table = '$bo_table' and mb_id = '$mb_id'";
    sql_query($sql);
    $msg = "삭제 하였습니다.";
}

alert($msg, "mw.board.member.php?bo_table=$bo_table{$qstr}");
?>
