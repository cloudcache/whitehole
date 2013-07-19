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
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

if ($mw_basic[cf_hot])
{
    if (!$mw_basic[cf_hot_limit]) $mw_basic[cf_hot_limit] = 10;
    ?>
    <style type="text/css">
    #mw_basic #mw_basic_hot_list li.hot_icon_1 { background:url(<?=$board_skin_path?>/img/icon_hot_1.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_2 { background:url(<?=$board_skin_path?>/img/icon_hot_2.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_3 { background:url(<?=$board_skin_path?>/img/icon_hot_3.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_4 { background:url(<?=$board_skin_path?>/img/icon_hot_4.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_5 { background:url(<?=$board_skin_path?>/img/icon_hot_5.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_6 { background:url(<?=$board_skin_path?>/img/icon_hot_6.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_7 { background:url(<?=$board_skin_path?>/img/icon_hot_7.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_8 { background:url(<?=$board_skin_path?>/img/icon_hot_8.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_9 { background:url(<?=$board_skin_path?>/img/icon_hot_9.gif) no-repeat left 2px; }
    #mw_basic #mw_basic_hot_list li.hot_icon_10 { background:url(<?=$board_skin_path?>/img/icon_hot_10.gif) no-repeat left 2px; }
    </style>
    <?
    switch ($mw_basic[cf_hot]) {
        case "1": $hot_start = ""; $hot_title = "실시간"; break;
        case "2": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*7); $hot_title = "주간"; break;
        case "3": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*30); $hot_title = "월간"; break;
        case "4": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24); $hot_title = "일간"; break;
        case "5": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*365); $hot_title = "연간"; break;
        case "6": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*30*3); $hot_title = "3개월"; break;
        case "7": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*30*6); $hot_title = "6개월"; break;
    }
    $sql_between = 1;
    if ($mw_basic[cf_hot] > 1) {
        $sql_between = " wr_datetime between '$hot_start' and '$g4[time_ymdhis]' ";
    }
    $sql_except = "";
    $tmp = explode("\n", $board[bo_notice]);
    for ($i=0, $m=sizeof($tmp); $i<$m; $i++) { 
        if (!trim($tmp[$i])) continue;
        $bo_notice[] = trim($tmp[$i]);
    }
    if (count($bo_notice)>0)
        $sql_except = " and wr_id not in (".implode(",", $bo_notice).") ";

    $hot_list = array();

    if ($mw_basic[cf_hot_basis] == 'file') {
        $sql_between = str_replace("wr_datetime", "bf_datetime", $sql_between);
        $sql = " select wr_id, sum(CAST(bf_download AS SIGNED)) as down from $g4[board_file_table] where bo_table = '$bo_table' and $sql_between $sql_except ";
        $sql.= " group by bo_table, wr_id order by down desc limit $mw_basic[cf_hot_limit]";
        $qry = sql_query($sql);
        while ($row = sql_fetch_array($qry)) {
            $hot_list[] = sql_fetch("select wr_id, wr_subject, wr_link1, wr_link_write from $write_table where wr_id = '$row[wr_id]'");
        }
    } else {
        $sql = " select wr_id, wr_subject, wr_link1, wr_link_write from $write_table where wr_is_comment = 0 and $sql_between $sql_except ";
        $sql.= " order by wr_{$mw_basic[cf_hot_basis]} desc limit $mw_basic[cf_hot_limit]";
        $qry = sql_query($sql);
        while ($row = sql_fetch_array($qry)) {
            $hot_list[] = $row;
        }
    }

    for ($i=0, $m=count($hot_list); $row=$hot_list[$i]; $i++)
    {
        $row[href] = "$g4[bbs_path]/board.php?bo_table=$bo_table&wr_id=$row[wr_id]";
        $row[link_href] = "$board_skin_path/link.php?bo_table=$bo_table&wr_id=$row[wr_id]&no=1";

        // 링크게시판
        if ($mw_basic[cf_link_board] && $row[wr_link1]) {
            if ($is_admin || ($row[mb_id] && $row[mb_id] == $member[mb_id]))
                ;
            else if ($member[mb_level] >= $mw_basic[cf_link_board])
                $row[href] = "javascript:void(window.open('{$row[link_href]}'))";    
            else
                $row[href] = "javascript:void(alert('권한이 없습니다.'))";    
        }

        if ($row[wr_link_write] && $row[wr_link1]) {
            if ($is_admin || ($row[mb_id] && $row[mb_id] == $member[mb_id]))
                ;
            else
                $row[href] = "javascript:void(window.open('{$row[link_href]}'))";
        }
        $hot_list[$i] = $row;
    }
    ?>
    <div id=mw_basic_hot_list>
        <h3> <?=$hot_title?> 인기 게시물 </h3>
        <ul class=mw_basic_hot_dot>
        <?
        for ($i=0, $m=count($hot_list); $row=$hot_list[$i]; $i++) {
            $row[wr_subject] = mw_reg_str($row[wr_subject]);
            $row[wr_subject] = bc_code($row[wr_subject], 0);
            ?>
            <li class=hot_icon_<?=($i+1)?>> 
                <nobr><a href="<?=$row[href]?>"><?=cut_str($row[wr_subject], 90)?></a></nobr>
            </li>
            <?
            if (($i+1)%($mw_basic[cf_hot_limit]/2)==0) echo "</ul><ul>";
        }
        ?>
        </ul>
        <div class="block"></div>
    </div>
    <?
} 


