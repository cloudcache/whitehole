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

$mw_is_list = true;

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

mw_bomb();

// 실명인증 & 성인인증
if ($mw_basic[cf_kcb_list] && !is_okname()) {
    check_okname();
} else {

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership")) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_list");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        mw_cash_alert_membership($is_membership);
        //alert("$is_membership 회원만 이용 가능합니다.");
}

// 지업로더로 업로드한 파일
// 하루지난 데이터 삭제 (글작성 완료되지 않은..)
if ($mw_basic[cf_guploader]) {
    $gup_old = date("Y-m-d H:i:s", $g4[server_time] - 86400);
    $sql = "select * from $mw[guploader_table] where bf_datetime <= '$gup_old'";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry)) {
        @unlink("$g4[path]/data/guploader/$row[bf_file]");
    }
    sql_query("delete from $mw[guploader_table] where bf_datetime <= '$gup_old'", false);
}

// 카테고리
$is_category = false;
if ($board[bo_use_category]) 
{
    $is_category = true;
    $category_location = "./board.php?bo_table=$bo_table&sca=";
    $category_option = mw_get_category_option($bo_table); // SELECT OPTION 태그로 넘겨받음
}

// page 변수 중복 제거
$qstr = preg_replace("/(\&page=.*)/", "", $qstr);
$write_pages = get_paging($config[cf_write_pages], $page, $total_page, "./board.php?bo_table=$bo_table".$qstr."&page=");

// 이전,다음 검색시 페이지 번호 제거
$prev_part_href = preg_replace("/(\&page=.*)/", "", $prev_part_href);
$next_part_href = preg_replace("/(\&page=.*)/", "", $next_part_href);

// 1:1 게시판
if ($mw_basic[cf_attribute] == "1:1" && !$is_admin) {
    require("$board_skin_path/mw.proc/mw.list.1n1.php");
}

// 익명 게시판
if ($mw_basic[cf_attribute] == "anonymous") {
    if (strstr($sfl, "mb_id") || strstr($sfl, "wr_name")) {
        alert("익명게시판에서는 아이디 또는 이름으로 검색하실 수 없습니다.");
    }
}

if ($mw_basic[cf_anonymous]) {
    if (strstr($sfl, "mb_id") || strstr($sfl, "wr_name")) {
        alert("익명작성이 가능한 게시판에서는 아이디 또는 이름으로 검색하실 수 없습니다.");
    }
}

// 쓰기버튼 항상 출력
if ($mw_basic[cf_write_button])
    $write_href = "./write.php?bo_table=$bo_table";

// 글쓰기 버튼에 분류저장
if ($sca && $write_href)
    $write_href .= "&sca=".urlencode($sca);

// 글쓰기 버튼 공지
if ($write_href && $mw_basic[cf_write_notice]) {
    $write_href = "javascript:btn_write_notice('$write_href');";
}

// 스킨설정버튼
$config_href = "javascript:mw_config()";

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 5;
if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;
if ($mw_basic[cf_reward]) $colspan+=3;
if ($mw_basic[cf_contents_shop]) $colspan++;
if ($mw_basic[cf_type] == "thumb") $colspan++;
if ($mw_basic[cf_type] == "gall") $colspan = $board[bo_gallery_cols];
if ($mw_basic[cf_attribute] == "qna") $colspan += 2;

// 목록 셔플
if ($mw_basic[cf_list_shuffle]) { // 공지사항 제외 처리
    $tmp_notice = array();
    $tmp_list = array();
    for ($i=0, $m=sizeof($list); $i<$m; $i++) {
        if ($list[$i][is_notice])
            $tmp_notice[] = $list[$i];
        else
            $tmp_list[] = $list[$i];
    }
    shuffle($tmp_list);
    $list = array_merge($tmp_notice, $tmp_list);
}

$list_count = sizeof($list);

$list_id = array();
for ($i=0; $i<$list_count; $i++) { $list_id[] = $list[$i][wr_id]; }

// 설문 아이콘 표시용
$vote_id = array();
if ($mw_basic[cf_vote] && $list_count) {
    $sql = "select wr_id, vt_id from $mw[vote_table] where bo_table = '$bo_table' and wr_id in (".implode(',', $list_id).")";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $vote_id[] = $row[wr_id];
        // 잘못된 설문 db 보완
        $row2 = sql_fetch("select count(*) as cnt from $mw[vote_item_table] where vt_id = '$row[vt_id]'");
        if (!$row2[cnt])
            sql_query("delete from $mw[vote_table] where vt_id = '$row[vt_id]'");
    }
}

// 퀴즈 아이콘 표시용
$quiz_id = array();
if ($mw_basic[cf_quiz] && $mw_quiz && $list_count) {
    $sql = "select wr_id, qz_id from $mw_quiz[quiz_table] where bo_table = '$bo_table' and wr_id in (".implode(',', $list_id).")";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry)) {
        $quiz_id[] = $row[wr_id];
    }
}

// 자폭 아이콘 표시용
$bomb_id = array();
if ($mw_basic[cf_bomb_level] && $list_count) {
    $sql = "select wr_id from $mw[bomb_table] where bo_table = '$bo_table' and wr_id in (".implode(',', $list_id).")";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry)) {
        $bomb_id[] = $row[wr_id];
    }
}

$new_time = date("Y-m-d H:i:s", $g4[server_time] - ($board[bo_new] * 3600));
$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 and wr_datetime >= '$new_time' ");
$new_count = $row[cnt];

// 제목이 두줄로 표시되는 경우 이 코드를 사용해 보세요.
// <nobr style='display:block; overflow:hidden; width:000px;'>제목</nobr>
?>

<? if ($mw_basic[cf_type] == "desc" || $mw_basic[cf_type] == "thumb") { // 요약형, 썸네일형일경우 제목 볼드 ?>
<style type="text/css">
#mw_basic .mw_basic_list_subject a { font-size:13px; font-weight:bold; }
</style>
<? } ?>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css?<?=filemtime("$board_skin_path/style.common.css")?>" type="text/css">
<? if ($mw_basic[cf_social_commerce]) { ?>
<link rel="stylesheet" href="<?=$social_commerce_path?>/style.css" type="text/css">
<? } ?>

<!--
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
-->
<link type="text/css" href="<?=$board_skin_path?>/mw.js/ui-lightness/jquery-ui-1.8.19.custom.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/jquery-ui-1.8.19.custom.min.js"></script>
<? if ($board[bo_use_list_view] && !$wr_id) { ?>
<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/tooltip.js"></script>
<? } ?>

<!-- 게시판 목록 시작 -->
<table width="<?=$bo_table_width?>" align="center" cellpadding="0" cellspacing="0"><tr><td id=mw_basic>

<? @include_once($mw_basic[cf_include_head]); ?>

<? include_once("$board_skin_path/mw.proc/mw.list.hot.skin.php"); ?>

<!-- 분류 셀렉트 박스, 게시물 몇건, 관리자화면 링크 -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr height="25">
    <td>
        <form name="fcategory" method="get" style="margin:0;">
        <? if ($is_category && !$mw_basic[cf_category_tab]) { ?>
            <select name=sca onchange="location='<?=$category_location?>'+this.value;">
            <option value=''>전체</option>
            <?=$category_option?>
            </select>
        <? } ?>
        <? if (($mw_basic[cf_type] == "gall" || $mw_basic[cf_social_commerce]) && $is_checkbox) { ?>
            <input onclick="if (this.checked) all_checked(true); else all_checked(false);" type=checkbox>
        <?}?>

        <? if ($write_href) { ?><a href="<?=$write_href?>"><img src="<?=$board_skin_path?>/img/btn_write.gif" border="0" align="absmiddle"></a><? } ?>
        </form>
    </td>
    <td align="right">
        <? if ($mw_basic[cf_social_commerce]) { ?>
        <span class=mw_basic_total style="cursor:pointer;" onclick="win_open('<?=$social_commerce_path?>/order_list.php?bo_table=<?=$bo_table?>', 'order_list', 'width=800,height=600,scrollbars=1');">[주문내역]</span>
        <? } ?>

        <? include("$board_skin_path/mw.proc/mw.smart-alarm-config.php") ?>
        <span class=mw_basic_total>총 게시물 <?=number_format($total_count)?>건, 최근 <?=number_format($new_count)?> 건</span>
        <? if ($is_admin && $mw_basic[cf_collect] && file_exists("$g4[path]/plugin/rss-collect/_lib.php")) {?>
        <img src="<?=$g4[path]?>/plugin/rss-collect/img/btn_collect.png" align="absmiddle" style="cursor:pointer;" onclick="win_open('<?=$g4[path]?>/plugin/rss-collect/config.php?bo_table=<?=$bo_table?>', 'rss_collect', 'width=800,height=600,scrollbars=1')">
        <? } ?>
        <a style="cursor:pointer" class="tooltip"
            title="읽기:<?=$board[bo_read_point]?>,
쓰기:<?=$board[bo_write_point]?><?
if ($mw_basic[cf_contents_shop_write]) { echo " ($mw_cash[cf_cash_name]$mw_basic[cf_contents_shop_write_cash]$mw_cash[cf_cash_unit])"; } ?>,
댓글:<?=$board[bo_comment_point]?>,
다운:<?=$board[bo_download_point]?>"><!--
        --><img src='<?=$board_skin_path?>/img/btn_info.gif' border=0 align=absmiddle></a>
        <? if ($mw_basic[cf_social_commerce] && $rss_href && file_exists("$social_commerce_path/img/xml.png")) { ?>
            <a href='<?=$social_commerce_path?>/xml.php?bo_table=<?=$bo_table?>'><img src='<?=$social_commerce_path?>/img/xml.png' border=0 align=absmiddle></a>
        <? } else if ($rss_href) { ?><a href='<?=$rss_href?>'><img src='<?=$board_skin_path?>/img/btn_rss.gif' border=0 align=absmiddle></a><?}?>
        <? if ($is_admin == "super") { ?><a href="<?=$config_href?>"><img src="<?=$board_skin_path?>/img/btn_config.gif" title="스킨설정" border="0" align="absmiddle"></a><?}?>
        <? if ($admin_href) { ?><a href="<?=$admin_href?>"><img src="<?=$board_skin_path?>/img/btn_admin.gif" title="관리자" width="63" height="22" border="0" align="absmiddle"></a><?}?>
    </td>
</tr>
<tr><td height=5></td></tr>
</table>

<? include_once("$board_skin_path/mw.proc/mw.notice.top.php") ?>

<? include_once("$board_skin_path/mw.proc/mw.search.top.php") ?>

<? include_once("$board_skin_path/mw.proc/mw.cash.membership.skin.php") ?>

<?
if ($is_category && $mw_basic[cf_category_tab]) {
    $category_list = explode("|", $board[bo_category_list]);
?>
<div class="category_tab">
<ul>
    <li <? if (!$sca) echo "class='selected'";?>><div><a href="<?=$g4[bbs_path]?>/board.php?bo_table=<?=$bo_table?>">전체</a></div></li>
    <? for ($i=0, $m=sizeof($category_list); $i<$m; $i++) { ?>
    <li <? if (urldecode($sca) == $category_list[$i]) echo "class='selected'";?>><div><a 
        href="<?=$g4[bbs_path]?>/board.php?bo_table=<?=$bo_table?>&sca=<?=urlencode($category_list[$i])?>"><?=$category_list[$i]?></a></div></li>
    <? } ?>
</ul>
</div>
<? } ?>

<!-- 제목 -->
<form name="fboardlist" id="fboardlist" method="post">
<input type='hidden' name='bo_table' value='<?=$bo_table?>'>
<input type='hidden' name='sfl'  value='<?=$sfl?>'>
<input type='hidden' name='stx'  value='<?=$stx?>'>
<input type='hidden' name='spt'  value='<?=$spt?>'>
<input type='hidden' name='sca'  value='<?=$sca?>'>
<input type='hidden' name='page' value='<?=$page?>'>
<input type='hidden' name='sw' id='sw'  value=''>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td colspan=<?=$colspan?> height=2 class=mw_basic_line_color></td></tr>
<? if ($mw_basic[cf_type] != "gall" && !$mw_basic[cf_social_commerce]) { ?>
<tr class=mw_basic_list_title>
    <? if (!$mw_basic[cf_post_num]) { ?><td width=50>번호</td><? } ?>
    <? if ($is_checkbox) { ?><td width=40><input onclick="if (this.checked) all_checked(true); else all_checked(false);" type=checkbox></td><?}?>
    <? if ($mw_basic[cf_type] == "thumb") { ?><td width=<?=$mw_basic[cf_thumb_width]+20?>> 이미지 </td><?}?>
    <td>제목</td>
    <? if ($mw_basic[cf_reward]) { ?> <td width=70>충전</td> <?}?>
    <? if ($mw_basic[cf_reward]) { ?> <td width=50>마감</td> <?}?>
    <? if ($mw_basic[cf_reward]) { ?> <td width=50>상태</td> <?}?>
    <? if ($mw_basic[cf_contents_shop]) { ?> <td width=80><?=$mw_cash[cf_cash_name]?></td> <?}?>
    <? if (!$mw_basic[cf_post_name]) { ?> <? if ($mw_basic[cf_attribute] != "anonymous") { ?> <td width=95>글쓴이</td> <?}?> <?}?>
    <? if ($mw_basic[cf_attribute] == "qna") { ?> <td width=50>상태</td> <?}?>
    <? if ($mw_basic[cf_attribute] == "qna" && $mw_basic[cf_qna_point_use]) { ?> <td width=40>포인트</td> <?}?>
    <? if (!$mw_basic[cf_post_date]) { ?> <td width=50>날짜</td> <?}?>
    <? if (!$mw_basic[cf_post_hit]) { ?> <td width=40><?=subject_sort_link('wr_hit', $qstr2, 1)?>조회</a></td> <?}?>
    <? if (!$mw_basic[cf_list_good]) { ?>
    <? if ($is_good) { ?><td width=40><?=subject_sort_link('wr_good', $qstr2, 1)?>추천</a></td><?}?>
    <? if ($is_nogood) { ?><td width=40><?=subject_sort_link('wr_nogood', $qstr2, 1)?>비추천</a></td><?}?>
    <? } ?>
</tr>
<tr><td colspan=<?=$colspan?> height=1 class=mw_basic_line_color></td></tr>
<? } ?>
<? if ($mw_basic[cf_type] == "gall") { ?> <tr><td colspan=<?=$colspan?> height=10></td></tr> <? } ?>

<!-- 목록 -->
<? $mw_membership = array(); ?>
<? $mw_membership_icon = array(); ?>

<? $line_number = 0; ?>
<? for ($i=0; $i<count($list); $i++) { ?>
<?
@include("$mw_basic[cf_include_list_main]");

mw_basic_move_cate($bo_table, $list[$i][wr_id]);

// 댓글감춤
if ($list[$i][wr_comment_hide])
    $list[$i][comment_cnt] = 0;

// 호칭
$list[$i][name] = get_name_title($list[$i][name], $list[$i][wr_name]);

// 자동치환
$list[$i][subject] = mw_reg_str($list[$i][subject]);

// BC코드
$list[$i][subject] = bc_code($list[$i][subject], 0);

// 멤버쉽 아이콘
if (function_exists("mw_cash_membership_icon") && $list[$i][mb_id] != $config[cf_admin])
{
    if (!in_array($list[$i][mb_id], $mw_membership)) {
        $mw_membership[] = $list[$i][mb_id];
        $mw_membership_icon[$list[$i][mb_id]] = mw_cash_membership_icon($list[$i][mb_id]);
        $list[$i][name] = $mw_membership_icon[$list[$i][mb_id]].$list[$i][name];
    } else {
        $list[$i][name] = $mw_membership_icon[$list[$i][mb_id]].$list[$i][name];
    }
}

// 익명
if ($list[$i][wr_anonymous]) {
    $list[$i][name] = "익명";
    $list[$i][wr_name] = $list[$i][name];
}

// 공지사항 상단
if ($mw_basic[cf_notice_top] && $mw_basic[cf_type] != 'gall') {
    if ($list[$i][is_notice]) continue;
    if (in_array($list[$i][wr_id], $notice_list) && !$stx) continue;
}

// 리워드
if ($mw_basic[cf_reward]) {
    $reward = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '{$list[$i][wr_id]}'");
    if ($reward[re_edate] != "0000-00-00" && $reward[re_edate] < $g4[time_ymd]) { // 날짜 지나면 종료
        sql_query("update $mw[reward_table] set re_status = '' where bo_table = '$bo_table' and wr_id = '{$list[$i][wr_id]}'");
        $reward[re_status] = '';
    }
    if ($reward[re_edate] == "0000-00-00")
        $reward[re_edate] = "&nbsp;";
    else
        $reward[re_edate] = substr($reward[re_edate], 5, 5);
}

// 컨텐츠샵
$mw_price = "";
if ($mw_basic[cf_contents_shop]) {
    if ($list[$i][is_notice])
        $mw_price = '&nbsp;';
    elseif (!$list[$i][wr_contents_price])
	$mw_price = "무료";
    else
	$mw_price = number_format($list[$i][wr_contents_price]).$mw_cash[cf_cash_unit];
}

// 링크로그
for ($j=1; $j<=$g4['link_count']; $j++)
{
    if ($mw_basic[cf_link_log])  {
        $list[$i]['link'][$j] = set_http(get_text($list[$i]["wr_link{$j}"]));
        $list[$i]['link_href'][$j] = "$board_skin_path/link.php?bo_table=$board[bo_table]&wr_id={$list[$i][wr_id]}&no=$j" . $qstr;
        $list[$i]['link_hit'][$j] = (int)$list[$i]["wr_link{$j}_hit"];
    }

    $list[$i]['link_target'][$j] = $list[$i]["wr_link{$j}_target"];
    if (!$list[$i]['link_target'][$j])
        $list[$i]['link_target'][$j] = '_blank';
}

// 링크게시판
if ($mw_basic[cf_link_board] && $list[$i][link_href][1]) {
    //if (!$is_admin && $member[mb_id] && $list[$i][mb_id] != $member[mb_id])
    if (!$list[$i][link][1] || $is_admin || ($list[$i][mb_id] && $list[$i][mb_id] == $member[mb_id]))
        ;
    else if ($member[mb_level] >= $mw_basic[cf_link_board]) {
        if ($list[$i][link_target][1] == '_blank')
            $list[$i][href] = "javascript:void(window.open('{$list[$i][link_href][1]}'))";    
        else
            $list[$i][href] = $list[$i][link_href][1];
    }
    else
        $list[$i][href] = "javascript:void(alert('권한이 없습니다.'))";
    $list[$i][wr_hit] = $list[$i][link_hit][1];
}

if ($list[$i][wr_link_write] && $list[$i][link_href][1]) {
    if (!$list[$i][link][1] || $is_admin || ($list[$i][mb_id] && $list[$i][mb_id] == $member[mb_id]))
        ;
    else {
        if ($list[$i][link_target][1] == '_blank')
            $list[$i][href] = "javascript:void(window.open('{$list[$i][link_href][1]}'))";    
        else
            $list[$i][href] = $list[$i][link_href][1];
    }
    $list[$i][wr_hit] = $list[$i][link_hit][1];
}

if ($board[bo_read_point] < 0 && $list[$i][mb_id] != $member[mb_id] && $is_member && !$is_admin && $mw_basic[cf_read_point_message]) {
    $tmp = sql_fetch(" select * from $g4[point_table] where mb_id = '$member[mb_id]' and po_rel_table = '$bo_table' and po_rel_id = '{$list[$i][wr_id]}' and po_rel_action = '읽기'");
    if (!$tmp) {
        $list[$i][href] = "javascript:if (confirm('글을 읽으시면 $board[bo_read_point] 포인트 차감됩니다.\\n(현재포인트 : $member[mb_point])')) location.href = '{$list[$i][href]}&point=1'";
    }
} 

// sns식 날짜표시
if ($mw_basic[cf_sns_datetime]) {
    $list[$i][datetime2] = "<span style='font-size:11px;'>".mw_basic_sns_date($list[$i][wr_datetime])."</span>";
}

// 공지사항 출력 항목
if ($mw_basic[cf_post_name]) $list[$i][name] = "";
if ($mw_basic[cf_post_date]) $list[$i][datetime2] = "";
if ($mw_basic[cf_post_hit]) $list[$i][wr_hit] = "";

if ($list[$i][is_notice]) {
    if ($mw_basic[cf_notice_name]) $list[$i][name] = "";
    if ($mw_basic[cf_notice_date]) $list[$i][datetime2] = "";
    if ($mw_basic[cf_notice_hit]) $list[$i][wr_hit] = "";
}

// 조회수, 추천수, 글번호에 세자리마다 컴마, 사용
if ($mw_basic[cf_comma]) {
    $list[$i][num] = @number_format($list[$i][num]);
    $list[$i][wr_hit] = @number_format($list[$i][wr_hit]);
    $list[$i][wr_good] = @number_format($list[$i][wr_good]);
    $list[$i][wr_nogood] = @number_format($list[$i][wr_nogood]);
}

// 신고된 게시물
$is_singo = false;
if ($list[$i][wr_singo] && $list[$i][wr_singo] >= $mw_basic[cf_singo_number] && $mw_basic[cf_singo_write_block]) {
    $list[$i][subject] = "신고가 접수된 게시물입니다.";
    $is_singo = true;
}

// 게시물 아이콘
$write_icon = '';
ob_start();
if ($is_singo)
    echo "<img src=\"$board_skin_path/img/icon_red.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
if ($list[$i][wr_view_block])
    echo "<img src=\"$board_skin_path/img/icon_view_block.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
elseif ($list[$i][wr_kcb_use])
    echo "<img src=\"$board_skin_path/img/icon_kcb.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
elseif (in_array($list[$i][wr_id], $quiz_id))
    echo "<img src=\"$quiz_path/img/icon_quiz.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
elseif (in_array($list[$i][wr_id], $bomb_id))
    echo "<img src=\"$board_skin_path/img/icon_bomb.gif\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
elseif (in_array($list[$i][wr_id], $vote_id))
    echo "<img src=\"$board_skin_path/img/icon_vote.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
elseif ($list[$i][wr_is_mobile])
    echo "<img src=\"$board_skin_path/img/icon_mobile.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\" width=13 height=12>&nbsp;";
else
    echo "<img src=\"$board_skin_path/img/icon_subject.gif\" align=absmiddle style=\"border-bottom:2px solid #fff;\" width=13 height=12>&nbsp;";
$write_icon = ob_get_contents();
ob_end_clean();

if ($mw_basic[cf_type] != "list")
{
    $set_width = $mw_basic[cf_thumb_width];
    $set_height = $mw_basic[cf_thumb_height];

    // 섬네일 생성
    $thumb_file = "";
    $file = mw_get_first_file($bo_table, $list[$i][wr_id], true);
    if (!empty($file)) {
        $source_file = "$file_path/{$file[bf_file]}";

        //if ($mw_basic[cf_img_1_noview])
        //    $thumb_file = "$file_path/{$file[bf_file]}";
        //else
            $thumb_file = "$thumb_path/{$list[$i][wr_id]}";

        if (!file_exists($thumb_file)) {
            mw_make_thumbnail($set_width, $set_height, $source_file, $thumb_file, $mw_basic[cf_thumb_keep]);
        } else {
            //if (!$mw_basic[cf_img_1_noview]) {
            if ($mw_basic[cf_thumb_keep]) {
                $size = @getImageSize($source_file);
                $size = mw_thumbnail_keep($size, $set_width, $set_height);
                $set_width = $size[0];
                $set_height = $size[1];
            } else
                $size = @getImageSize($thumb_file);

            if ($size[0] != $set_width || $size[1] != $set_height) {
                mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height], $source_file, $thumb_file, $mw_basic[cf_thumb_keep]);
                if ($mw_basic[cf_thumb2_width])
                    @mw_make_thumbnail($mw_basic[cf_thumb2_width], $mw_basic[cf_thumb2_height], $source_file,
                        "{$thumb2_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb2_keep]);
                if ($mw_basic[cf_thumb3_width])
                    @mw_make_thumbnail($mw_basic[cf_thumb3_width], $mw_basic[cf_thumb3_height], $source_file,
                        "{$thumb3_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb3_keep]);
                if ($mw_basic[cf_thumb4_width])
                    @mw_make_thumbnail($mw_basic[cf_thumb4_width], $mw_basic[cf_thumb4_height], $source_file,
                        "{$thumb4_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb4_keep]);
                if ($mw_basic[cf_thumb5_width])
                    @mw_make_thumbnail($mw_basic[cf_thumb5_width], $mw_basic[cf_thumb5_height], $source_file,
                        "{$thumb5_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb5_keep]);
            }
        //}
        }
    } else {
        $thumb_file = "$thumb_path/{$list[$i][wr_id]}";
        if (!file_exists($thumb_file)) {
            preg_match("/<img.*src=\"(.*)\"/iU", $list[$i][wr_content], $match);
            if ($match[1]) {
                $match[1] = str_replace($g4[url], "..", $match[1]);
                if (file_exists($match[1])) {
                    mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height], $match[1],
                        $thumb_file, $mw_basic[cf_thumb_keep]);
                    if ($mw_basic[cf_thumb2_width])
                        @mw_make_thumbnail($mw_basic[cf_thumb2_width], $mw_basic[cf_thumb2_height], $match[1],
                            "{$thumb2_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb2_keep]);
                    if ($mw_basic[cf_thumb3_width])
                        @mw_make_thumbnail($mw_basic[cf_thumb3_width], $mw_basic[cf_thumb3_height], $match[1],
                            "{$thumb3_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb3_keep]);
                    if ($mw_basic[cf_thumb4_width])
                        @mw_make_thumbnail($mw_basic[cf_thumb4_width], $mw_basic[cf_thumb4_height], $match[1],
                            "{$thumb4_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb4_keep]);
                    if ($mw_basic[cf_thumb5_width])
                        @mw_make_thumbnail($mw_basic[cf_thumb5_width], $mw_basic[cf_thumb5_height], $match[1],
                            "{$thumb5_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb5_keep]);   
                }
            }
        }
    }
}

if ($mw_basic[cf_social_commerce])
{
    include("$social_commerce_path/list.skin.php");    
}
else if ($mw_basic[cf_type] == "gall")
{
    if ($list[$i][is_notice]) continue;

    if (!file_exists($thumb_file) || $list[$i][icon_secret]) {
        $thumb_file = "$board_skin_path/img/noimage.gif";
        $thumb_width = "width='$mw_basic[cf_thumb_width]'";
        $thumb_height = "height='$mw_basic[cf_thumb_height]'";
    } else {
        $thumb_width = "";
        $thumb_height = "";
    }

    $style = "";
    $class = "";
    if ($list[$i][is_notice]) $style = " class=mw_basic_list_notice";

    if ($wr_id == $list[$i][wr_id]) { // 현재위치
        $style = " class=mw_basic_list_num_select";
        $class = " select";
    }

    $td_width = (int)(100 / $board[bo_gallery_cols]);

    // 제목스타일
    if ($mw_basic[cf_subject_style])
        $style .= " style='font-family:{$list[$i][wr_subject_font]}; color:{$list[$i][wr_subject_color]}'";

    $list[$i][subject] = "<span{$style}>{$list[$i][subject]}</span></a>";

    if (($line_number+1)%$colspan==1) echo "<tr>";
?>
    <td width="<?=$td_width?>%" class="mw_basic_list_gall <?=$class?>">
        <? if ($is_checkbox) { ?>
        <div style="text-align:left; width:<?=$set_width+18?>px; margin:0 auto 0 auto;'"><!--
            --><input type="checkbox" name="chk_wr_id[]" value="<?=$list[$i][wr_id]?>"></div>
        <? } ?>
        <div><a href="<?=$list[$i][href]?>"><img src="<?=$thumb_file?>" <?=$thumb_width?> <?=$thumb_height?> align=absmiddle></a></div>
        <div class="mw_basic_list_subject_gall"
            <? if (!$mw_basic[cf_thumb_keep]) echo "style='width:".($set_width+10)."px; text-align:left;'"; ?>>
        <? if ($is_category && $list[$i][ca_name]) { ?>
            <div style="margin:0 0 5px 0;"><a href="<?=$list[$i][ca_name_href]?>"
                class=mw_basic_list_category>[<?=$list[$i][ca_name]?>]</a></div>
        <? } ?>
        <?=$write_icon?><a href="<?=$list[$i][href]?>"><?=$list[$i][subject]?></a>
        <? if ($list[$i][comment_cnt]) { ?>
            <a href="<?=$list[$i][comment_href]?>" class=mw_basic_list_comment_count>+<?=$list[$i][wr_comment]?></a>
        <? } ?>
        </div>
    </td>
    <? if (($line_number+1)%$colspan==0) echo "</tr>"; ?>

<? } else { // $mw_basic[cf_type] == "gall" ?>

<tr align=center <? if ($list[$i][is_notice]) echo "bgcolor='#f8f8f9'"; ?>>

    <!-- 글번호 -->
    <? if (!$mw_basic[cf_post_num]) { ?>
    <td>
        <?
	if ($list[$i][is_notice] && $mw_basic[cf_notice_hit]) $list[$i][wr_hit] = "";

        if ($list[$i][is_notice]) // 공지사항
            echo "<img src=\"$board_skin_path/img/icon_notice.gif\" width=30 height=16>";
        else if ($wr_id == $list[$i][wr_id]) // 현재위치
            echo "<span class=mw_basic_list_num_select>{$list[$i][num]}</span>";
        else // 일반
            echo "<span class=mw_basic_list_num>{$list[$i][num]}</span>";
        ?>
    </td>
    <? } ?>

    <? if ($is_checkbox) { ?>
    <!-- 관리자용 체크박스 -->
    <td> <input type=checkbox name=chk_wr_id[] value="<?=$list[$i][wr_id]?>"> </td>
    <? } ?>

    <? if ($mw_basic[cf_type] == "thumb") { ?>
    <? if (!file_exists($thumb_file) || $list[$i][icon_secret]) $thumb_file = "$board_skin_path/img/noimage.gif"; ?>

    <!-- 썸네일 -->
    <td class=mw_basic_list_thumb><!-- 여백제거
        --><a href="<?=$list[$i][href]?>"><img src="<?=$thumb_file?>" width=<?=$mw_basic[cf_thumb_width]?> height=<?=$mw_basic[cf_thumb_height]?> align=absmiddle></a><!--
    --></td>
    <? } ?>

    <!-- 글제목 -->
    <td class=mw_basic_list_subject>
        <?
        if ($mw_basic[cf_type] == "desc" && file_exists($thumb_file)) {
            echo "<div class=mw_basic_list_thumb>";
            echo "<a href=\"{$list[$i][href]}\"><img src=\"{$thumb_file}\" width={$mw_basic[cf_thumb_width]} height={$mw_basic[cf_thumb_height]} align=absmiddle></a>";
            echo "</div>";
        }
        if ($mw_basic[cf_type] == "desc") {
            echo "<div class=mw_basic_list_subject_desc>";
        }
        echo $list[$i][reply];
        echo $list[$i][icon_reply];
        if ($is_category && $list[$i][ca_name]) {
            echo "<a href=\"{$list[$i][ca_name_href]}\" class=mw_basic_list_category>[{$list[$i][ca_name]}]</a>&nbsp;";
        }

        if ($mw_basic[cf_read_level] && $list[$i][wr_read_level])
            echo "<span class=mw_basic_list_level>[{$list[$i][wr_read_level]}레벨]</span>&nbsp;";

        $style = "";
        if ($list[$i][is_notice]) $style = " class=mw_basic_list_notice";

        if ($wr_id == $list[$i][wr_id]) // 현재위치
            $style = " class=mw_basic_list_num_select";

        //if ($mw_basic[cf_type] == "list") {
        echo $write_icon;
        //}
        if (!$mw_basic[cf_subject_link] || $board[bo_read_level] <= $member[mb_level]) {
            if (!$mw_basic[cf_board_member] || ($mw_basic[cf_board_member] && $mw_basic[cf_board_member_view]) || $mw_is_board_member || $is_admin) {
                echo "<a href=\"{$list[$i][href]}\">";
            }
        }

        // 제목스타일
        if ($mw_basic[cf_subject_style])
            $style .= " style='font-family:{$list[$i][wr_subject_font]}; color:{$list[$i][wr_subject_color]}'";

        echo "<span{$style}>{$list[$i][subject]}</span></a>";

        if ($list[$i][comment_cnt])
            //echo " <span class=mw_basic_list_comment_count>{$list[$i][comment_cnt]}</span>";
            //echo " <a href=\"{$list[$i][comment_href]}\" class=mw_basic_list_comment_count>{$list[$i][comment_cnt]}</a>";
            echo " <a href=\"{$list[$i][comment_href]}\" class=mw_basic_list_comment_count>+{$list[$i][wr_comment]}</a>";

        echo " " . $list[$i][icon_new];
        echo " " . $list[$i][icon_file];
        echo " <a target='_blank' href='{$list[$i][link][1]}'>" . $list[$i][icon_link] ."</a>";
        echo " " . $list[$i][icon_hot];
        echo " " . $list[$i][icon_secret];

        if ($mw_basic[cf_type] == "desc") {
            echo "</div>";
            $desc = strip_tags($list[$i][wr_content]);
            $desc = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "", $desc);
            $desc = mw_reg_str($desc);
            $desc = cut_str($desc, $mw_basic[cf_desc_len]);
            echo "<div class=mw_basic_list_desc> $desc </div>";
        }
        ?>
    </td>
    <? if ($mw_basic[cf_reward]) { ?>
    <td class=mw_basic_list_reward_point><?=number_format($reward[re_point])?> P</td>
    <td class=mw_basic_list_reward_edate><?=$reward[re_edate]?></td>
    <td class=mw_basic_list_reward_status><img src="<?=$board_skin_path?>/img/btn_reward_<?=$reward[re_status]?>.gif" align="absmiddle"></td>
    <? } ?>
    <? if ($mw_basic[cf_contents_shop]) { ?>
        <td class=mw_basic_list_contents_price><span><?=$mw_price?></span></td><?}?>
    <? if (!$mw_basic[cf_post_name]) { ?>
    <? if ($mw_basic[cf_attribute] != "anonymous") { ?> <td><nobr class=mw_basic_list_name><?=$list[$i][name]?></nobr></td> <?}?> <?}?>
    <? if ($mw_basic[cf_attribute] == 'qna') { ?>
        <td class=mw_basic_list_qna_status><div><img src="<?=$board_skin_path?>/img/icon_qna_<?=$list[$i][wr_qna_status]?>.png"></div></td> <?}?>
    <? if ($mw_basic[cf_attribute] == 'qna' && $mw_basic[cf_qna_point_use]) { ?> <td class=mw_basic_list_point><?=$list[$i][wr_qna_point]?></span></td> <?}?>
    <? if (!$mw_basic[cf_post_date]) { ?> <td class=mw_basic_list_datetime><?=$list[$i][datetime2]?></span></td> <?}?>
    <? if (!$mw_basic[cf_post_hit]) { ?> <td class=mw_basic_list_hit><?=$list[$i][wr_hit]?></span></td> <?}?>
    <? if (!$mw_basic[cf_list_good]) { ?>
    <? if ($is_good) { ?><td class=mw_basic_list_good><?=$list[$i][wr_good]?></td><? } ?>
    <? if ($is_nogood) { ?><td class=mw_basic_list_nogood><?=$list[$i][wr_nogood]?></td><? } ?>
    <? } ?>
</tr>
<? if ($i<count($list)-1) { // 마지막 라인 출력 안함 ?>
<!--<tr><td colspan=<?=$colspan?> height=1 bgcolor=#E7E7E7></td></tr>-->
<tr><td colspan=<?=$colspan?> height=1 style="border-top:1px dotted #e7e7e7"></td></tr>
<?}?>
<?}?>
<?  $line_number++; ?>
<?} //$mw_basic[cf_type] == "gall" else?>


<? if (count($list) == 0) { echo "<tr><td colspan={$colspan} class=mw_basic_nolist>게시물이 없습니다.</td></tr>"; } ?>
<tr><td colspan=<?=$colspan?> class=mw_basic_line_color height=1></td></tr>
</table>

</form>

<!-- 페이지 -->
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
    <td class=mw_basic_page>
        <? //if ($prev_part_href) { echo "<a href='$prev_part_href' class='img'><img src='$board_skin_path/img/btn_search_prev.gif' border=0 align=absmiddle title='이전검색'></a>"; } ?>
        <? if ($prev_part_href) { echo "<a href='$prev_part_href'>이전검색</a>"; } ?>
        <?
        // 기본으로 넘어오는 페이지를 아래와 같이 변환하여 이미지로도 출력할 수 있습니다.
        //echo $write_pages;
        /*
        $write_pages = str_replace("처음", "<img src='$board_skin_path/img/page_begin.gif' border='0' align='absmiddle' title='처음'>", $write_pages);
        $write_pages = str_replace("이전", "<img src='$board_skin_path/img/page_prev.gif' border='0' align='absmiddle' title='이전'>", $write_pages);
        $write_pages = str_replace("다음", "<img src='$board_skin_path/img/page_next.gif' border='0' align='absmiddle' title='다음'>", $write_pages);
        $write_pages = str_replace("맨끝", "<img src='$board_skin_path/img/page_end.gif' border='0' align='absmiddle' title='맨끝'>", $write_pages);
        */
        echo $write_pages;
        ?>
        <?// if ($next_part_href) { echo "<a href='$next_part_href' class='img'><img src='$board_skin_path/img/btn_search_next.gif' border=0 align=absmiddle title='다음검색'></a>"; } ?>
        <? if ($next_part_href) { echo "<a href='$next_part_href'>다음검색</a>"; } ?>
    </td>
</tr>
</table>

<!-- 링크 버튼, 검색 -->
<table width=100%>
<tr>
    <td height="40">
        <? if ($list_href) { ?><a href="<?=$list_href?>"><img src="<?=$board_skin_path?>/img/btn_list.gif" border="0" align="absmiddle"></a><? } ?>
        <? if ($write_href) { ?><a href="<?=$write_href?>"><img src="<?=$board_skin_path?>/img/btn_write.gif" border="0" align="absmiddle"></a><? } ?>
        <? /*if ($is_checkbox) { ?>
            <a href="javascript:select_delete();"><img src="<?=$board_skin_path?>/img/btn_select_delete.gif" border="0"></a>
            <a href="javascript:select_copy('copy');"><img src="<?=$board_skin_path?>/img/btn_select_copy.gif" border="0"></a>
            <a href="javascript:select_copy('move');"><img src="<?=$board_skin_path?>/img/btn_select_move.gif" border="0"></a>
            <a href="javascript:mw_move_cate();"><img src="<?=$board_skin_path?>/img/btn_select_cate.gif" border="0"></a>
        <? }*/ ?>

        <? if ($is_checkbox) { ?>
        <script type="text/javascript">
        function admin_select_action(v) {
            switch (v) {
                case 'delete': select_delete(); break;
                case 'copy': select_copy('copy'); break;
                case 'move': select_copy('move'); break;
                case 'cate': mw_move_cate(); break;
                case 'notice_up': mw_notice('up'); break;
                case 'notice_down': mw_notice('down'); break;
                case 'qna_0': mw_qna(0); break;
                case 'qna_1': mw_qna(1); break;
                case 'qna_2': mw_qna(2); break;
            }
        }
        </script>
        <select id="admin_action" onchange="admin_select_action(this.value)" style="font-size:11px; height:22px;">
            <option>==글관리==</option>
            <option value='delete'> 선택 삭제 </option>
            <option value='copy'> 선택 복사 </option>
            <option value='move'> 선택 이동 </option>
            <option value='cate'> 선택 분류이동 </option>
            <option value='notice_up'> 선택 공지올림 </option>
            <option value='notice_down'> 선택 공지내림 </option>
            <? if ($mw_basic[cf_attribute] == 'qna') { ?>
            <option value='qna_0'> 선택 질문 미해결 </option>
            <option value='qna_1'> 선택 질문 해결 </option>
            <option value='qna_2'> 선택 질문 보류 </option>
            <? } ?>
        </select>
        <? } ?>
    </td>
    <td align="right">
        <form name=fsearch method=get>
        <input type=hidden name=bo_table value="<?=$bo_table?>">
        <input type=hidden name=sca value="<?=$sca?>">
        <select name=sfl>
            <option value='wr_subject'>제목</option>
            <option value='wr_content'>내용</option>
            <option value='wr_subject||wr_content'>제목+내용</option>
            <? if ($mw_basic[cf_attribute] != "anonymous" && !$mw_basic[cf_anonymous]) { ?>
            <option value='mb_id,1'>회원아이디</option>
            <option value='mb_id,0'>회원아이디(코)</option>
            <option value='wr_name,1'>이름</option>
            <option value='wr_name,0'>이름(코)</option>
            <? } ?>
        </select>
        <input name=stx maxlength=15 size=10 itemname="검색어" required value='<?=stripslashes($stx)?>'>
        <select name=sop>
            <option value=and>and</option>
            <option value=or>or</option>
        </select>
        <input type=image src="<?=$board_skin_path?>/img/btn_search.gif" border=0 align=absmiddle>
        </form>
    </td>
</tr>
</table>

<? @include_once($mw_basic[cf_include_tail]); ?>

</td></tr></table>


<script type="text/javascript">
<?  if (!$mw_basic[cf_category_tab]) { ?>
if ('<?=$sca?>') document.fcategory.sca.value = '<?=urlencode($sca)?>';
<? } ?>
if ('<?=$stx?>') {
    document.fsearch.sfl.value = '<?=$sfl?>';
    document.fsearch.sop.value = '<?=$sop?>';
}
</script>

<? if ($mw_basic[cf_write_notice]) { ?>
<script type="text/javascript">
// 글쓰기버튼 공지
function btn_write_notice(url) {
    var msg = "<?=$mw_basic[cf_write_notice]?>";
    if (confirm(msg))
	location.href = url;
}
</script>
<? } ?>


<? if ($is_checkbox) { ?>
<script type="text/javascript">

<? if ($is_admin == "super") { ?>
function mw_config() {
    win_open("<?=$board_skin_path?>/mw.adm/mw.config.php?bo_table=<?=$bo_table?>", "config", "width=980, height=700, scrollbars=yes");
}
<? } ?>

function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function check_confirm(str) {
    var f = document.fboardlist;
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(str + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }
    return true;
}

// 선택한 게시물 삭제
function select_delete() {
    var f = document.fboardlist;

    $("#admin_action").val('');

    str = "삭제";
    if (!check_confirm(str))
        return;

    if (!confirm("선택한 게시물을 정말 "+str+" 하시겠습니까?\n\n한번 "+str+"한 자료는 복구할 수 없습니다"))
        return;

    f.action = "./delete_all.php";
    f.submit();
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    $("#admin_action").val('');

    if (sw == "copy")
        str = "복사";
    else
        str = "이동";

    if (!check_confirm(str))
        return;

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = "<?=$board_skin_path?>/move.php";
    //f.action = "./move.php";
    f.submit();
}

// 선택한 게시물 분류 변경
function mw_move_cate() {
    var f = document.fboardlist;

    $("#admin_action").val('');

    if (!check_confirm("분류이동"))
        return;

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.target = "move";
    f.action = "<?=$board_skin_path?>/mw.proc/mw.move.cate.php";
    f.submit();
}

function mw_notice(sw) {
    $("#admin_action").val('');

    if (sw == 'up') {
        if (!confirm("공지로 등록하시겠습니까?")) {
            return false;
        }
    } else {
        if (!confirm("공지를 내리시겠습니까?")) {
            return false;
        }
    }

    $("#sw").val(sw);

    $.post("<?=$board_skin_path?>/mw.proc/mw.notice.check.php", $("#fboardlist").serialize(), function(data) {
        alert(data);
        location.reload();
    });
}

function mw_qna(sw) {
    $("#admin_action").val('');

    var m = '';

    switch (sw) {
        case 0: m = '미해결'; break;
        case 1: m = '해결'; break;
        case 2: m = '보류'; break;
    }
    if (!confirm("질문을 " + m + " 처리 하시겠습니까?")) { return false; }

    $("#sw").val(sw);

    $.post("<?=$board_skin_path?>/mw.proc/mw.qna.check.php", $("#fboardlist").serialize(), function(data) {
        alert(data);
        location.reload();
    });
}
</script>
<? } ?>

<style type="text/css">
<?=$mw_basic[cf_css]?>
</style>
<!-- 게시판 목록 끝 -->

<?
// 팝업공지
$sql = "select * from $mw[popup_notice_table] where bo_table = '$bo_table' order by wr_id desc";
$qry = sql_query($sql, false);
while ($row = sql_fetch_array($qry)) {
    $row2 = sql_fetch("select * from $write_table where wr_id = '$row[wr_id]'");
    if (!$row2) {
        sql_query("delete from $mw[popup_notice_table] where bo_table = '$bo_table' and wr_id = '$row[wr_id]'");
        continue;
    }
    $view = get_view($row2, $board, $board_skin_path, 255);
    mw_board_popup($view, $html);
}

} // 실명인증

// RSS 수집기
if ($mw_basic[cf_collect] == 'rss' && $rss_collect_path && file_exists("$rss_collect_path/_config.php")) {
    include_once("$rss_collect_path/_config.php");
    if ($mw_rss_collect_config[cf_license]) {
        ?>
        <script type="text/javascript">
        $(document).ready(function () {
            $.get("<?=$rss_collect_path?>/ajax.php?bo_table=<?=$bo_table?>");
        });
        </script>
        <?
    }
}

