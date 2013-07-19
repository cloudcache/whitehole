<?php
/**
 * MW Builder LITE for Gnuboard4
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

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

if (!$theme && get_cookie("ck_theme"))
    $theme = get_cookie("ck_theme");

if ($theme) {
    if (preg_match("/^[a-z0-9_-]+$/i", $theme)) {
        $g4['mw_lite_theme'] = $theme;
        set_cookie("ck_theme", $theme, 60*60*24*30);
    }

    $theme_path = "{$g4['path']}/theme/{$g4['mw_lite_theme']}";
    if (!is_dir($theme_path) || !file_exists($theme_path)) {
        $g4['mw_lite_theme'] = 'basic';
    }
}

if ($gr_id && !$bo_table && strstr($_SERVER[PHP_SELF], "$g4[bbs]/group.php")) {
    $sql = " select bo_table from $g4[board_table] where gr_id = '$gr_id' ";
    $qry = sql_query($sql);
    if (mysql_num_rows($qry) == 1) {
        $row = sql_fetch_array($qry);
        goto_url("$g4[bbs_path]/board.php?bo_table=$row[bo_table]");
    }
}

if (strstr($_SERVER[PHP_SELF], "$g4[admin]/board_form.php")) {
    sql_query("update $g4[board_table] set bo_1_subj = '출력순서'");
    sql_query("update $g4[board_table] set bo_2_subj = '사용자정의URL'");
}

if ($bo_table && $board[bo_2] && strstr($_SERVER[PHP_SELF], "$g4[bbs]/board.php")) {
    if (!strstr($board[bo_2], "http"))
        $url = "$g4[path]/$board[bo_2]";
    else
        $url = $board[bo_2];

    goto_url($url);
}

