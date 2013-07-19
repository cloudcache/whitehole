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

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");
include_once("$board_skin_path/mw.lib/mw.sms.lib.php");
include_once("$g4[path]/lib/etc.lib.php");

$wr_content = mw_spelling($wr_content);
$wr_subject = mw_spelling($wr_subject);

// 실명인증
if ($mw_basic[cf_kcb_write] && !is_okname()) {
    if ($mw_basic[cf_kcb_type] == "okname") {
        alert("실명인증 후 이용하실 수 있습니다.");
    } else {
        alert("성인인증 후 이용하실 수 있습니다.");
    }
}

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership")) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_write");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        alert("$is_membership 회원만 이용 가능합니다.");
}

if ($mw_basic[cf_must_notice]) { // 공지 필수
    $tmp_notice = str_replace("\n", ",", trim($board[bo_notice]));
    $cnt_notice = sizeof(explode(",", $tmp_notice));

    if ($tmp_notice) {
        $sql = "select count(*) as cnt from $mw[must_notice_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and wr_id in ($tmp_notice)";
        $row = sql_fetch($sql);
        if ($row[cnt] != $cnt_notice)
            alert("$board[bo_subject] 공지를 모두 읽으셔야 글작성이 가능합니다.");
    }
}

// 한 사람당 글 한개만 등록가능
if (($w == "" || $w == "r") && $mw_basic[cf_only_one] && !$is_admin) {
    if ($is_member)
	$sql = "select * from $write_table where wr_is_comment = 0 and mb_id = '$member[mb_id]'";
    else
	$sql = "select * from $write_table where wr_is_comment = 0 and wr_ip = '$_SERVER[REMOTE_ADDR]'";
    $row = sql_fetch($sql);
    if ($row)
	alert("이 게시판은 한 사람당 글 한개만 등록 가능합니다.");
}

// 글작성 조건 
if (($w == "" || $w == "r") && $mw_basic[cf_write_point] && !$is_admin) {
    if ($member[mb_point] < $mw_basic[cf_write_point]) {
        alert("이 게시판은 $mw_basic[cf_write_point] 포인트 이상 소지자만 작성 가능합니다.");
    }
}
if (($w == "" || $w == "r") && $mw_basic[cf_write_register] && !$is_admin) {
    $gap = ($g4[server_time] - strtotime($member[mb_datetime])) / (60*60*24);
    if ($gap < $mw_basic[cf_write_register]) {
        alert("이 게시판은 가입후 $mw_basic[cf_write_register] 일이 지나야 작성 가능합니다.");
    }
}

// 글작성 제한
if (($w == "" || $w == "r") && $mw_basic[cf_write_day] && $mw_basic[cf_write_day_count] && !$is_admin) {
    $old = date("Y-m-d 00:00:00", $g4[server_time]-((60*60*24)*($mw_basic[cf_write_day]-1)));
    $sql = "select count(wr_id) as cnt from $write_table where mb_id = '$member[mb_id]' and wr_is_comment = '0' ";
    $sql.= "and wr_datetime between '$old' and '$g4[time_ymd] 23:59:59'";
    $row = sql_fetch($sql);

    if ($row[cnt] >= $mw_basic[cf_write_day_count]) {
        alert("이 게시판은 $mw_basic[cf_write_day]일에 $mw_basic[cf_write_day_count]번만 작성 가능합니다.");
    }
}

// 컨텐츠샵 다운로드 결제 설정 체크
if (!$is_admin and $mw_basic[cf_contents_shop] == '1' and $mw_basic[cf_contents_shop_max] && $mw_basic[cf_contents_shop_min]) { 
    if ($wr_contents_price < $mw_basic[cf_contents_shop_min] or $wr_contents_price > $mw_basic[cf_contents_shop_max]) {
        alert("컨텐츠 가격은 $mw_cash[cf_cash_name] $mw_basic[cf_contents_shop_min]$mw_cash[cf_cash_unit] 이상 $mw_basic[cf_contents_shop_max]$mw_cash[cf_cash_unit] 이하로 입력해주세요.");
    }
}

if (!$is_admin and $mw_basic[cf_contents_shop] == '1' and $mw_basic[cf_contents_shop_fix] and $w == 'u') {
    if ($write[wr_contents_price] != $wr_contents_price) {
        alert("컨텐츠 판매가격을 수정할 수 없습니다. 사이트 관리자에게 문의해주세요.");
        $wr_contents_price = $write[wr_contents_price];
    }
}

// 컨텐츠샵 글작성 캐쉬 차감
if ($mw_basic[cf_contents_shop_write] && $w == "") { 
    if ($mw_basic[cf_contents_shop_write_cash] > $mw_cash[mb_cash]) {
        alert("보유하신 $mw_cash[cf_cash_name] 부족합니다.\\n\\n충전 후 이용해주세요.");
    }
}

// 자동치환권한
if ($mw_basic[cf_replace_word] > $member[mb_level]) { 
    if (strstr($wr_subject, "{닉네임}") || strstr($wr_content, "{닉네임}")) {
        alert("{닉네임} 코드를 사용하실 수 없습니다.");
    }
    if (strstr($wr_subject, "{별명}") || strstr($wr_content, "{별명}")) {
        alert("{별명} 코드를 사용하실 수 없습니다.");
    }
}

// 질문게시판
if ($mw_basic[cf_attribute] == 'qna' && $mw_basic[cf_qna_point_use] && $w == '') {
    if ($wr_qna_point < $mw_basic[cf_qna_point_min] && !$is_admin)
        alert("질문 포인트가 너무 작습니다. $mw_basic[cf_qna_point_min] 포인트 이상으로 입력해주세요. ");

    if ($wr_qna_point > $mw_basic[cf_qna_point_max] && !$is_admin)
        alert("질문 포인트가 너무 큽니다. $mw_basic[cf_qna_point_max] 포인트 이하로 입력해주세요. ");

    if ($wr_qna_point < 0 && !$is_admin)
        alert("질문 포인트는 0 보다 큰수로 입력해주세요.");

    if ($member[mb_point] < $wr_qna_point)
        alert("보유하신 포인트가 질문 포인트 보다 적습니다.");

    if ($mw_basic[cf_qna_count] && !$is_admin) {
        $tmp = sql_fetch("select count(*) as cnt from $write_table where wr_qna_status = '0' and mb_id = '$member[mb_id]'");
        if ($tmp[cnt] >= $mw_basic[cf_qna_count]) {
            alert("이전에 작성하셨던 미해결 질문을 해결 또는 보류처리 해주셔야\\n\\n새로운 질문을 등록할 수 있습니다.");
        }
    }
}

if (!$is_admin && $write[wr_view_block])
    alert("이 게시물 보기는 차단되었습니다. 관리자만 접근 가능합니다.");

// 재능마켓
if ($mw_basic[cf_talent_market]) include("$talent_market_path/write_update.head.skin.php");

$watermark_files = array();

$sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_width > 0  order by bf_no";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    $watermark_files[] = mw_watermark_file("$file_path/$row[bf_file]");
}

// 일반회원 공지글 수정시 공지 내려가는 현상 보완 (그누보드 버그)
$is_notice = preg_match("/[^0-9]{0,1}{$write['wr_id']}[\r]{0,1}/", $board['bo_notice']);

