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

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

// 실명인증 & 성인인증
if ($mw_basic[cf_kcb_write] && !is_okname()) {
    check_okname();
} else if ($w == "r" && ($mw_basic[cf_kcb_read] || $write[wr_kcb_use]) && !is_okname()) {
    check_okname();
} else {

if ($mw_basic[cf_read_level] && $write[wr_read_level] > $member[mb_level]) {
    alert("글을 읽을 권한이 없습니다.");
}

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership")) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_write");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        mw_cash_alert_membership($is_membership);
        //alert("$is_membership 회원만 이용 가능합니다.");
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

// 질문게시판
if ($mw_basic[cf_attribute] == 'qna' && $mw_basic[cf_qna_point_use] && $w == '') {
    if ($mw_basic[cf_qna_count] && !$is_admin) {
        $tmp = sql_fetch("select count(*) as cnt from $write_table where wr_qna_status = '0' and mb_id = '$member[mb_id]'");
        if ($tmp[cnt] >= $mw_basic[cf_qna_count]) {
            alert("이전에 작성하셨던 미해결 질문을 해결 또는 보류처리 해주셔야\\n\\n새로운 질문을 등록할 수 있습니다.",
                "$g4[bbs_path]/board.php?bo_table=$bo_table&sfl=mb_id&stx=$member[mb_id]");
        }
    }
}

if (!$is_admin && $write[wr_view_block])
    alert("이 게시물 보기는 차단되었습니다. 관리자만 접근 가능합니다.");

if (!$mw_basic[cf_editor])
    $mw_basic[cf_editor] = "cheditor";

// 관리자만 dhtml 사용
if ($mw_basic[cf_admin_dhtml] && $is_admin) $is_dhtml_editor = true;

// 모바일 접근시 에디터 사용안함
if (preg_match("/(iphone|samsung|lgte|mobile|BlackBerry|android|windows ce|mot|SonyEricsson)/i", $_SERVER[HTTP_USER_AGENT])) {
    $is_dhtml_editor = false;
}

// TEXT 로 작성된 글 에디터로 수정할 때 한줄로 나오는 문제해결
$html = 0;
if (strstr($write['wr_option'], "html1")) $html = 1;
if (strstr($write['wr_option'], "html2")) $html = 2;

if (($html == 0 || $html == 2) && $is_dhtml_editor) {
    if ($w != '' || !$board[bo_insert_content]) {
        $content = nl2br($content);
    }
}

if ($w != "u") {
    $write[wr_zzal] = "짤방";
}

// 글수정 페이지의 첨부파일명 길이 조정
//--------------------------------------------------------------------------
// 가변 파일
$file_script = "";
$file_length = -1;
// 수정의 경우 파일업로드 필드가 가변적으로 늘어나야 하고 삭제 표시도 해주어야 합니다.
if ($w == "u")
{
    for ($i=0; $i<$file[count]; $i++)
    {
        $row = sql_fetch(" select bf_file, bf_content from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$i' ");
        if ($row[bf_file])
        {
            $file_script .= "add_file(\"&nbsp;&nbsp;<a href='{$file[$i][href]}'>".cut_str($file[$i][source], 20)."({$file[$i][size]})</a> <input type='checkbox' name='bf_file_del[$i]' value='1'> 파일 삭제하려면 체크하세요.";
            if ($is_file_content)
                //$file_script .= "<br><input type='text' class=ed size=50 name='bf_content[$i]' value='{$row[bf_content]}' title='업로드 이미지 파일에 해당 되는 내용을 입력하세요.'>";
                // 첨부파일설명에서 ' 또는 " 입력되면 오류나는 부분 수정
                $file_script .= "<br><input type='text' class=ed size=50 name='bf_content[$i]' value='".addslashes(get_text($row[bf_content]))."' title='업로드 이미지 파일에 해당 되는 내용을 입력하세요.'>";
            $file_script .= "\");\n";
        }
        else
            $file_script .= "add_file('');\n";
    }
    $file_length = $file[count] - 1;
}
if ($file_length < 0)
{
    $file_script .= "add_file('');\n";
    $file_length = 0;
}

if ($w == "") {  // 첨부파일 기본갯수
    for ($i=0; $i<$mw_basic[cf_attach_count]-1; $i++) {
        $file_script .= "add_file();\n";
    }   
}

// 스킨설정버튼
$config_href = "javascript:mw_config()";

// RSS 버튼
$rss_href = "";
if ($board[bo_use_rss_view])
    $rss_href = "./rss.php?bo_table=$bo_table";

$admin_href = "";
// 최고관리자 또는 그룹관리자라면
if ($member[mb_id] && ($is_admin == 'super' || $group[gr_admin] == $member[mb_id])) 
    $admin_href = "$g4[admin_path]/board_form.php?w=u&bo_table=$bo_table";

// 분류 사용 여부
$is_category = false;
if ($board[bo_use_category]) 
{
    $is_category = true;
    $category_location = "./board.php?bo_table=$bo_table&sca=";
    $category_option = get_category_option($bo_table); // SELECT OPTION 태그로 넘겨받음
}

// 분류 선택 또는 검색어가 있다면
if (!$total_count && ($sca || $stx))
{
    $sql_search = get_sql_search($sca, $sfl, $stx, $sop);

    // 가장 작은 번호를 얻어서 변수에 저장 (하단의 페이징에서 사용)
    $sql = " select MIN(wr_num) as min_wr_num from $write_table ";
    $row = sql_fetch($sql);
    $min_spt = $row[min_wr_num];

    if (!$spt) $spt = $min_spt;

    $sql_search .= " and (wr_num between '".$spt."' and '".($spt + $config[cf_search_part])."') ";

    // 원글만 얻는다. (코멘트의 내용도 검색하기 위함)
    $sql = " select distinct wr_parent from $write_table where $sql_search ";
    $result = sql_query($sql);
    $total_count = mysql_num_rows($result);
} 
else 
{
    $sql_search = "";

    $total_count = $board[bo_count_write];
}

if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") {
    /* $g4[cheditor4_path] = "$board_skin_path/cheditor";
    include_once("$board_skin_path/mw.lib/mw.cheditor.lib.php");
    echo "<script type='text/javascript' src='$board_skin_path/cheditor/cheditor.js'></script>";
    echo cheditor1('wr_content', '100%', '250'); */
    include_once("$g4[path]/lib/cheditor4.lib.php");
    echo "<script src='$g4[cheditor4_path]/cheditor.js'></script>";
    echo cheditor1('wr_content', '100%', '250');
}

if ($w == '' && trim($mw_basic[cf_insert_subject])) {
    $subject = $mw_basic[cf_insert_subject];
}

$new_time = date("Y-m-d H:i:s", $g4[server_time] - ($board[bo_new] * 3600));
$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 and wr_datetime >= '$new_time' ");
$new_count = $row[cnt];


if (($mw_basic[cf_attribute] == "anonymous" || ($w == 'u' && $write[wr_anonymous])) && $is_admin) {
    $is_name = false;
    $is_password = false;
    $is_email = false;
    $is_homepage = false;
}
?>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css?<?=filemtime("$board_skin_path/style.common.css")?>" type="text/css">
<!--
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
-->
<link type="text/css" href="<?=$board_skin_path?>/mw.js/ui-lightness/jquery-ui-1.8.19.custom.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/jquery-ui-1.8.19.custom.min.js"></script>

<?
//==============================================================================
// jquery date picker
//------------------------------------------------------------------------------
// 참고) ie 에서는 년, 월 select box 를 두번씩 클릭해야 하는 오류가 있습니다.
//------------------------------------------------------------------------------
// jquery-ui.css 의 테마를 변경해서 사용할 수 있습니다.
// base, black-tie, blitzer, cupertino, dark-hive, dot-luv, eggplant, excite-bike, flick, hot-sneaks, humanity, le-frog, mint-choc, overcast, pepper-grinder, redmond, smoothness, south-street, start, sunny, swanky-purse, trontastic, ui-darkness, ui-lightness, vader
// 아래 css 는 date picker 의 화면을 맞추는 코드입니다.
?>
<style type="text/css">
<!--
.ui-datepicker { font:12px dotum; }
.ui-datepicker select.ui-datepicker-month, 
.ui-datepicker select.ui-datepicker-year { width: 70px;}
.ui-datepicker-trigger { margin:0 0 -5px 2px; }
-->
</style>
<script type="text/javascript">
/* Korean initialisation for the jQuery calendar extension. */
/* Written by DaeKwon Kang (ncrash.dk@gmail.com). */
jQuery(function($){
        $.datepicker.regional['ko'] = {
                closeText: '닫기',
                prevText: '이전달',
                nextText: '다음달',
                currentText: '오늘',
                monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
                '7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
                monthNamesShort: ['1월','2월','3월','4월','5월','6월',
                '7월','8월','9월','10월','11월','12월'],
                dayNames: ['일','월','화','수','목','금','토'],
                dayNamesShort: ['일','월','화','수','목','금','토'],
                dayNamesMin: ['일','월','화','수','목','금','토'],
                weekHeader: 'Wk',
                dateFormat: 'yy-mm-dd',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['ko']);

    $('#vt_sdate').datepicker({
        showOn: 'button',
        buttonImage: '<?=$board_skin_path?>/img/calendar.gif',
        buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99'
    }); 


    $('#vt_edate').datepicker({
        showOn: 'button',
        buttonImage: '<?=$board_skin_path?>/img/calendar.gif',
        buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99'
    }); 

    $('#re_edate').datepicker({
        showOn: 'button',
        buttonImage: '<?=$board_skin_path?>/img/calendar.gif',
        buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99'
    }); 
});
</script>
<?
//==============================================================================
?>

<!-- 글작성 시작 -->
<table width="<?=$bo_table_width?>" align="center" cellpadding="0" cellspacing="0"><tr><td id=mw_basic>

<? @include_once($mw_basic[cf_include_head]); ?>

<script type="text/javascript">
// 글자수 제한
var char_min = parseInt(<?=$write_min?>); // 최소
var char_max = parseInt(<?=$write_max?>); // 최대
</script>

<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/tooltip.js"></script>

<? include_once("$board_skin_path/mw.proc/mw.cash.membership.skin.php") ?>

<!-- 분류 셀렉트 박스, 게시물 몇건, 관리자화면 링크 -->
<table width="100%">
<tr height="25">
    <td width="30%">
        <form name="fcategory" method="get" style="margin:0;">
        <? if ($is_category && !$mw_basic[cf_category_tab]) { ?>
            <select name=sca onchange="location='<?=$category_location?>'+this.value;">
            <option value=''>전체</option>
            <?=$category_option?>
            </select>
        <? } ?>
        <? if ($mw_basic[cf_type] == "gall" && $is_checkbox) { ?><input onclick="if (this.checked) all_checked(true); else all_checked(false);" type=checkbox><?}?>
        </form>
    </td>
    <td align="right">
        <? if ($mw_basic[cf_social_commerce]) { ?>
        <span class=mw_basic_total style="cursor:pointer;" onclick="win_open('<?=$social_commerce_path?>/order_list.php?bo_table=<?=$bo_table?>', 'order_list', 'width=800,height=600,scrollbars=1');">[주문내역]</span>
        <? } ?>
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


<!--<form name="fwrite" method="post" action="javascript:fwrite_check(document.fwrite);" enctype="multipart/form-data">-->
<form name="fwrite" method="post" onsubmit="return fwrite_check(document.fwrite);" enctype="multipart/form-data">
<input type=hidden name=null>
<input type=hidden name=w        value="<?=$w?>">
<input type=hidden name=bo_table value="<?=$bo_table?>">
<input type=hidden name=wr_id    value="<?=$wr_id?>">
<input type=hidden name=sca      value="<?=$sca?>">
<input type=hidden name=sfl      value="<?=$sfl?>">
<input type=hidden name=stx      value="<?=$stx?>">
<input type=hidden name=spt      value="<?=$spt?>">
<input type=hidden name=sst      value="<?=$sst?>">
<input type=hidden name=sod      value="<?=$sod?>">
<input type=hidden name=page     value="<?=$page?>">

<?
// 익명게시판
if ($mw_basic[cf_attribute] == "anonymous" && $is_guest) {
    $is_name = $is_email = $is_homepage = false;
    echo "<input type=hidden name=wr_name value='익명'>\n";
} 

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
<? } ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<colgroup width=100>
<colgroup width=''>
<tr><td colspan=2 height=2 class=mw_basic_line_color></td></tr>
<tr><td style="padding-left:20px" colspan=2 height=30 bgcolor=#f8f8f9><strong><?=$title_msg?></strong></td></tr>

<? if ($mw_basic[cf_contents_shop_write]) { ?>
<tr>
<td class=mw_basic_write_title>· <?=$mw_cash[cf_cash_name]?> </td>
<td>
    글작성시 <?=$mw_cash[cf_cash_name]?> <?=$mw_basic[cf_contents_shop_write_cash]?> <?=$mw_cash[cf_cash_unit]?> 차감됩니다.
    <span style="color:#888;">(나의 <?=$mw_cash[cf_cash_name]?> <?=number_format($mw_cash[mb_cash])?> <?=$mw_cash[cf_cash_unit]?>
        ⇒ <a href="<?=$g4[path]?>/plugin/cybercash/index.php" target="_blank">충전하기</a>)</span>
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_name) { ?>
<tr>
<td class=mw_basic_write_title>· 이름</td>
<td><input maxlength=20 size=15 name=wr_name itemname="이름" required value="<?=$name?>" class=mw_basic_text></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_password) { ?>
<tr>
<td class=mw_basic_write_title>· 패스워드</td>
<td><input type=password maxlength=20 size=15 name=wr_password itemname="패스워드" <?=$password_required?> class=mw_basic_text></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_email) { ?>
<tr>
<td class=mw_basic_write_title>· 이메일</td>
<td><input maxlength=100 size=50 name=wr_email email itemname="이메일" value="<?=$email?>" class=mw_basic_text></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_homepage) { ?>
<tr>
<td class=mw_basic_write_title>· 홈페이지</td>
<td><input size=50 name=wr_homepage itemname="홈페이지" value="<?=$homepage?>" class=mw_basic_text></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_dhtml_editor) { ?>
<input type=hidden value="html1" name="html">
<? } ?>

<? if ($is_notice || ($is_html && !$is_dhtml_editor) || $is_secret || $is_mail || $mw_basic[cf_anonymous]) { ?>
<tr>
<td class=mw_basic_write_title>· 옵션</td>
<td><? if ($is_notice) { ?><input type=checkbox name=notice value="1" <?=$notice_checked?>>공지&nbsp;<? } ?>
    <? if ($is_html) { ?>
    <input onclick="html_auto_br(this);" type=checkbox value="<?=$html_value?>"
        name="html" <?=$html_checked?>><span class=w_title>html</span>&nbsp;
    <? } ?>
    <? if ($is_secret) { ?>
        <? if ($is_admin || $is_secret==1) { ?>
        <input type=checkbox value="secret" name="secret" <?=$secret_checked?>><span class=w_title>비밀글</span>&nbsp;
        <? } else { ?>
        <input type=hidden value="secret" name="secret">
        <? } ?>
    <? } ?>
    <? if ($is_mail) { ?><input type=checkbox value="mail" name="mail" <?=$recv_email_checked?>>답변메일받기&nbsp;<? } ?>
    <? if ($mw_basic[cf_anonymous]) {?>
    <input type="checkbox" name="wr_anonymous" value="1" <?if ($write[wr_anonymous]) echo 'checked';?>> 익명
    <? } ?>
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<?  if ($mw_basic[cf_social_commerce]) include("$social_commerce_path/write.skin.php"); ?>
<?  if ($mw_basic[cf_talent_market]) include("$talent_market_path/write.skin.php"); ?>

<? if ($is_category) { ?>
<tr>
<td class=mw_basic_write_title>· 분류</td>
<td>
<?
if ($mw_basic[cf_category_radio]) {
    $category_list = explode("|", $board[bo_category_list]);
    if ($is_admin) {
        ?> <input type="radio" name="ca_name" value="공지" id="ca_name_1000"> <label for="ca_name_1000">공지 </label> <?
    }
    for ($i=0, $m=sizeof($category_list); $i<$m; $i++) { 
        ?> <input type="radio" name="ca_name" value="<?=$category_list[$i]?>" id="ca_name_<?$i?>"> <label for="ca_name_<?=$i?>"><?=$category_list[$i]?> </label> <?
    } 
    if ($w == "u") {
        ?>
        <script type="text/javascript">
        for (i=0; i<fwrite.ca_name.length; i++) {
            if (fwrite.ca_name[i].value == "<?=$write[ca_name]?>")
                fwrite.ca_name[i].checked = true;
        }
        </script>
        <?
    }
} else { ?>
<select name=ca_name required itemname="분류"><option value="">선택하세요<?=$category_option?></select>
<? } ?>
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<tr>
<td class=mw_basic_write_title>· 제목</td>
<td><input style="width:98%;" name=wr_subject id="wr_subject" itemname="제목" required value="<?=$subject?>" class=mw_basic_text></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>

<? if ($mw_basic[cf_subject_style] && $mw_basic[cf_subject_style_level] <= $member[mb_level]) { ?>
<tr>
<td class=mw_basic_write_title>· 제목 모양</td>
<td>

    글꼴 : <select name="wr_subject_font" id="wr_subject_font">
    <option value="">글꼴</option>
    <option value="">----</option>
    <option value="굴림">굴림</option>
    <option value="돋움">돋움</option>
    <option value="바탕">바탕</option>
    <option value="궁서">궁서</option>
    </select>

    <? if ($mw_basic[cf_subject_style_color_picker]) { ?>
    색상 : <input type="text" size="7" class="ed" name="wr_subject_color" id="wr_subject_color"/>
    <input type="button" class="btn1" value="색상 선택기▼" id="btn_color_picker" style="font-size:11px;"/>
    <div id="color_picker" style="position:absolute; display:none; padding:10px; background-color:#fff; border:1px solid #ccc; z-index:999;"></div>

    <? if (!$write[wr_subject_color]) $write[wr_subject_color] = $mw_basic[cf_subject_style_color_default]; ?>
    <script type="text/javascript" src="<?=$board_skin_path?>/mw.js/colorpicker/farbtastic.js"></script>
    <link rel="stylesheet" href="<?=$board_skin_path?>/mw.js/colorpicker/farbtastic.css" type="text/css" />
    <script type="text/javascript">
    fwrite.wr_subject_font.value = "<?=$write[wr_subject_font]?>";
    fwrite.wr_subject_color.value = "<?=$write[wr_subject_color]?>";

    $(document).ready(function() {
        $('#btn_color_picker').click(function () {
            $('#color_picker').toggle();
            if ($(this).val() == "색상 선택기▲")
                $(this).val("색상 선택기▼");
            else
                $(this).val("색상 선택기▲");
        });

        $('#color_picker').farbtastic('#wr_subject_color');
    });
    </script>
    <? } else { ?>
    <select name="wr_subject_color" id="wr_subject_color">
    <option value="">색상</option>
    <option value="">----</option>
    <option value="#000000" style="color:#000000;">검정</option>
    <option value="#ff9900" style="color:#ff9900;">주황</option>
    <option value="#b3a14d" style="color:#b3a14d;">노랑</option>
    <option value="#3cb371" style="color:#3cb371;">초록</option>
    <option value="#0033ff" style="color:#0033ff;">파랑</option>
    <option value="#000099" style="color:#000099;">남색</option>
    <option value="#9900cc" style="color:#9900cc;">보라</option>
    </select>
    <script type="text/javascript"> fwrite.wr_subject_color.value = "<?=$write['wr_subject_color']?>"; </script>
    <? } ?>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<tr>
<td class=mw_basic_write_title>· 내용</td>
<td style='padding:5 0 5 0;'>
    <? if (!$is_dhtml_editor) { ?>
    <table width=100%>
    <tr>
        <td align=left valign=bottom>
            <span style="cursor: pointer;" onclick="textarea_decrease('wr_content', 10);"><img src="<?=$board_skin_path?>/img/btn_up.gif"></span>
            <span style="cursor: pointer;" onclick="textarea_original('wr_content', 10);"><img src="<?=$board_skin_path?>/img/btn_init.gif"></span>
            <span style="cursor: pointer;" onclick="textarea_increase('wr_content', 10);"><img src="<?=$board_skin_path?>/img/btn_down.gif"></span>
            <? if ($mw_basic[cf_post_emoticon]) {?>
                <span class=mw_basic_comment_emoticon><a 
                    href="javascript:win_open('<?=$board_skin_path?>/mw.proc/mw.emoticon.skin.php?bo_table=<?=$bo_table?>','emo'
                    ,'width=600,height=400,scrollbars=yes')">☞ 이모티콘</a></span>
            <? } ?>
        </td>
        <td align=right><? if ($write_min || $write_max) { ?><span id=char_count></span>글자<?}?></td>
    </tr>
    </table>
    <? } ?>

    <? if (!$is_dhtml_editor || $mw_basic[cf_editor] != "cheditor") { ?>
    <textarea id="wr_content" name="wr_content" style='width:98%; word-break:break-all;' rows=10 itemname="내용" required  class=mw_basic_textarea
    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "geditor") echo "geditor"; ?>
    <? if ($write_min || $write_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?}?>><?=$content?></textarea>
    <? if (($write_min || $write_max) && !$is_dhtml_editor) { ?><script type="text/javascript"> check_byte('wr_content', 'char_count'); </script><?}?>
    <? } ?>

    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") echo cheditor2('wr_content', $content); ?>
    <div><input type="button" class="btn1" value="임시저장" onclick="mw_save_temp('임시 저장 했습니다.')"/></div>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>

<? if ($mw_basic[cf_contents_shop] == '2') { ?>
<tr>
<td class=mw_basic_write_title>· 컨텐츠 요약</td>
<td class=mw_basic_write_content>
    <? sql_query("alter table $write_table add wr_contents_preview text not null after wr_contents_price", false); ?>
    <div style="padding:5px 0 5px 0;">
        <span style="cursor: pointer;" onclick="textarea_decrease('wr_contents_preview', 10);"><img src="<?=$board_skin_path?>/img/btn_up.gif"></span>
        <span style="cursor: pointer;" onclick="textarea_original('wr_contents_preview', 10);"><img src="<?=$board_skin_path?>/img/btn_init.gif"></span>
        <span style="cursor: pointer;" onclick="textarea_increase('wr_contents_preview', 10);"><img src="<?=$board_skin_path?>/img/btn_down.gif"></span>
        <? if ($mw_basic[cf_post_emoticon]) {?>
            <span class=mw_basic_comment_emoticon><a 
                href="javascript:win_open('<?=$board_skin_path?>/mw.proc/mw.emoticon.skin.php?bo_table=<?=$bo_table?>','emo'
                ,'width=600,height=400,scrollbars=yes')">☞ 이모티콘</a></span>
        <? } ?>
    </div>

    <textarea id="wr_contents_preview" name="wr_contents_preview" style='width:98%; word-break:break-all;' rows=5 itemname="내용" class=mw_basic_textarea><?=$write[wr_contents_preview]?></textarea>
    <div> ※ 유료컨텐츠 홍보 내용을 간략히 작성해주세요. 무료컨텐츠의 경우 입력하실 필요가 없습니다.</div>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>
<? if ($mw_basic[cf_contents_shop]) { ?>
<tr>
<td class=mw_basic_write_title>· <?=$mw_cash[cf_cash_name]?></td>
<td class=mw_basic_write_content>
    <input type="text" size=10 name="wr_contents_price"
        numeric itemname="컨텐츠 가격" value="<?=$write[wr_contents_price]?>" class="mw_basic_text" <?
        if (!$is_admin) echo ' required ';
        if (!$is_admin and $mw_basic[cf_contents_shop_max] and $mw_basic[cf_contents_shop_min])
            echo ' onblur="contents_price_check(this)" ';
        if (!$is_admin and $w == 'u' and $mw_basic[cf_contents_shop_fix])
            echo ' readonly style="background-color:#efefef;" ';
        ?>>
    <?=$mw_cash[cf_cash_unit]?> (컨텐츠 가격<?
    if ($mw_basic[cf_contents_shop_max] and $mw_basic[cf_contents_shop_min]) {
        echo ", $mw_cash[cf_cash_name] $mw_basic[cf_contents_shop_min] $mw_cash[cf_cash_unit] 이상 ~ ";
        echo "  $mw_basic[cf_contents_shop_max] $mw_cash[cf_cash_unit] 이하"; 
    }
    if ($mw_basic[cf_contents_shop_uploader_cash]) {
        echo ", 업로더 수익 $mw_basic[cf_contents_shop_uploader_cash]%";
    }
    ?>)
    <? if ($mw_basic[cf_contents_shop_max] and $mw_basic[cf_contents_shop_min]) { ?>
    <script type="text/javascript">
    function contents_price_check(obj) {
        var price = Number(obj.value);
        if (price == '') return;
        else if (!price) {
            alert("컨텐츠 가격을 올바로 입력해주세요.");
            obj.select();
            return;
        }
        else if (price < <?=$mw_basic[cf_contents_shop_min]?> || price > <?=$mw_basic[cf_contents_shop_max]?>) {
            alert("컨텐츠 가격은 <?=$mw_cash[cf_cash_name]?> <?=$mw_basic[cf_contents_shop_min]?><?=$mw_cash[cf_cash_unit]?> 이상 <?=$mw_basic[cf_contents_shop_max]?><?=$mw_cash[cf_cash_unit]?> 이하로 입력해주세요.");
            obj.select();
            return;
        }
    }
    </script>
    <? } ?>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? if ($mw_basic[cf_contents_shop] == '1') { ?>
<tr>
<td class=mw_basic_write_title>· 사용도메인 </td>
<td class=mw_basic_write_content>
    <input type="checkbox" name="wr_contents_domain" id="wr_contents_domain" itemname="컨텐츠 사용도메인" value="1">
    <label for="wr_contents_domain">컨텐츠 구입시 사용도메인을 입력 받습니다.</label>
    <script type="text/javascript"> document.fwrite.wr_contents_domain.checked = "<?=$write[wr_contents_domain]?>" </script>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } } ?>

<? if ($mw_basic[cf_bomb_level] && $mw_basic[cf_bomb_level] <= $member[mb_level]) { ?>
<tr>
<td class=mw_basic_write_title>· 자동폭파 </td>
<td class=mw_basic_write_content>
    <?
    $bomb = array();
    $bm_year_start = date("Y", $g4[server_time]);
    if ($w == 'u') {
        $bomb = sql_fetch(" select * from $mw[bomb_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ");
        if (date("Y", strtotime($bomb[bm_datetime])) < date("Y", $g4[server_time])) {
            $bm_year_start = date("Y", strtotime($bomb[bm_datetime]));
        }
    }
    ?>
    <select name="bm_year">
        <option value=""></option>
        <? for ($i=$bm_year_start; $i<=date("Y", $g4[server_time])+1; $i++) { ?>
        <option value="<?=$i?>"><?=$i?></option>
        <? } ?>
    </select> 년
    <select name="bm_month">
        <option value=""></option>
        <? for ($i=1; $i<=12; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 월
    <select name="bm_day">
        <option value=""></option>
        <? for ($i=1; $i<=31; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 일 
    <select name="bm_hour">
        <option value=""></option>
        <? for ($i=0; $i<=23; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 시 
    <select name="bm_minute">
        <option value=""></option>
        <? for ($i=0; $i<=59; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 분 
    <input type="button" value="지금" class="btn1" onclick="bomb_cate_now()"/>
    <input type="button" value="초기화" class="btn1" onclick="bomb_cate_init()"/>
    <input type="checkbox" name="bm_log" value="1">흔적 남기기
    <script type="text/javascript">
    function bomb_cate_now() {
        var d = new Date();

        d.setTime(d.getTime()+1000*60);
        yy = d.getFullYear();
        mm = (d.getMonth() + 1);
        dd = d.getDate();
        hh = d.getHours();
        ii = d.getMinutes();

        if (mm < 10) mm = '0' + mm;
        if (dd < 10) dd = '0' + dd;
        if (hh < 10) hh = '0' + hh;
        if (ii < 10) ii = '0' + ii;

        fwrite.bm_year.value = yy;
        fwrite.bm_month.value = mm;
        fwrite.bm_day.value = dd;
        fwrite.bm_hour.value = hh;
        fwrite.bm_minute.value = ii;
    }
    function bomb_cate_init() {
        fwrite.bm_year.value = '';
        fwrite.bm_month.value = '';
        fwrite.bm_day.value = '';
        fwrite.bm_hour.value = '';
        fwrite.bm_minute.value = '';
    }
    <? if ($bomb) { ?>
    fwrite.bm_year.value = '<?=date("Y", strtotime($bomb[bm_datetime]))?>';
    fwrite.bm_month.value = '<?=date("m", strtotime($bomb[bm_datetime]))?>';
    fwrite.bm_day.value = '<?=date("d", strtotime($bomb[bm_datetime]))?>';
    fwrite.bm_hour.value = '<?=date("H", strtotime($bomb[bm_datetime]))?>';
    fwrite.bm_minute.value = '<?=date("i", strtotime($bomb[bm_datetime]))?>';
    fwrite.bm_log.checked = '<?=$bomb[bm_log]?>';
    <? } ?>
    </script>
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_move_level] && $mw_basic[cf_move_level] <= $member[mb_level]) { ?>
<tr>
<td class=mw_basic_write_title>· 이동예약 </td>
<td class=mw_basic_write_content>
    <?
    $move = array();
    $mv_year_start = date("Y", $g4[server_time]);
    if ($w == 'u') {
        $move = sql_fetch(" select * from $mw[move_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ");
        if (date("Y", strtotime($move[mv_datetime])) < date("Y", $g4[server_time])) {
            $mv_year_start = date("Y", strtotime($move[mv_datetime]));
        }
    }
    ?>
    <select name="mv_year">
        <option value=""></option>
        <? for ($i=$mv_year_start; $i<=date("Y", $g4[server_time])+1; $i++) { ?>
        <option value="<?=$i?>"><?=$i?></option>
        <? } ?>
    </select> 년
    <select name="mv_month">
        <option value=""></option>
        <? for ($i=1; $i<=12; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 월
    <select name="mv_day">
        <option value=""></option>
        <? for ($i=1; $i<=31; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 일 
    <select name="mv_hour">
        <option value=""></option>
        <? for ($i=0; $i<=23; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 시 
    <select name="mv_minute">
        <option value=""></option>
        <? for ($i=0; $i<=59; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
        <? } ?>
    </select> 분 
    <input type="button" value="지금" class="btn1" onclick="move_cate_now()"/>
    <input type="button" value="초기화" class="btn1" onclick="move_cate_init()"/>

    <br/>
    <? if ($category_option) { ?>
    분류를
    <select name="mv_cate">
        <option value=''>=분류선택=</option>
        <?=$category_option?>
    </select> 으로 이동,
    <? } ?>
    공지를
    <select name="mv_notice">
        <option value="">=선택=</option>
        <option value="u"> 올림 </option>
        <option value="d"> 내림 </option>
    </select>
    <script type="text/javascript">
    function move_cate_now() {
        var d = new Date();

        d.setTime(d.getTime()+1000*60);
        yy = d.getFullYear();
        mm = (d.getMonth() + 1);
        dd = d.getDate();
        hh = d.getHours();
        ii = d.getMinutes();

        if (mm < 10) mm = '0' + mm;
        if (dd < 10) dd = '0' + dd;
        if (hh < 10) hh = '0' + hh;
        if (ii < 10) ii = '0' + ii;

        fwrite.mv_year.value = yy;
        fwrite.mv_month.value = mm;
        fwrite.mv_day.value = dd;
        fwrite.mv_hour.value = hh;
        fwrite.mv_minute.value = ii;
    }
    function move_cate_init() {
        fwrite.mv_year.value = '';
        fwrite.mv_month.value = '';
        fwrite.mv_day.value = '';
        fwrite.mv_hour.value = '';
        fwrite.mv_minute.value = '';
    }
    <? if ($move) { ?>
    fwrite.mv_year.value = '<?=date("Y", strtotime($move[mv_datetime]))?>';
    fwrite.mv_month.value = '<?=date("m", strtotime($move[mv_datetime]))?>';
    fwrite.mv_day.value = '<?=date("d", strtotime($move[mv_datetime]))?>';
    fwrite.mv_hour.value = '<?=date("H", strtotime($move[mv_datetime]))?>';
    fwrite.mv_minute.value = '<?=date("i", strtotime($move[mv_datetime]))?>';
    fwrite.mv_cate.value = '<?=$move[mv_cate]?>';
    fwrite.mv_notice.value = '<?=$move[mv_notice]?>';
    <? } ?>
    </script>
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_quiz] && $mw_basic[cf_quiz_level] <= $member[mb_level]) { ?>
<tr>
<td class=mw_basic_write_title>· 퀴즈 </td>
<td class=mw_basic_write_content>
    <input type="button" class="btn1" value="퀴즈설정" onclick="win_quiz()"/>
    <input type="button" class="btn1" value="퀴즈삭제" onclick="del_quiz()"/>
    <input type="hidden" name="qz_id" id="qz_id" value=""/>
    <script type="text/javascript">
    function win_quiz() {
        wq = window.open("<?=$g4[path]?>/plugin/quiz/write.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&qz_id="+$("#qz_id").val(),
             "quiz", "width=700,height=400,scrollbars=yes");
        wq.focus();
    }
    function del_quiz() {
        if (!confirm("정말 퀴즈를 삭제하시겠습니까?")) return;
        $.get("<?=$quiz_path?>/delete.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&qz_id="+$("#qz_id").val(), function (str) {
            $("#qz_id").val('');
            alert(str);
        });
    }
    </script>
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_google_map]) { ?>
<tr>
<td class=mw_basic_write_title>· 지도삽입</td>
<td class=mw_basic_write_content>
    <input size="50" name=wr_google_map id="wr_google_map" itemname="지도삽입 주소" value="<?=$write[wr_google_map]?>" class=mw_basic_text>
    <input type="button" value="주소확인" class="btn1" onclick="win_google_map()">
    <div>(본문에 {구글지도} 라고 입력하면 원하는 위치에 구글지도가 삽입됩니다.)</div>

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript" src="<?=$board_skin_path?>/mw.js/mw.google.js"></script>
    <script type="text/javascript">
    function win_google_map() {
        $("#google-map").css("display", "none");
        $("#dialog-map").dialog("close");
        $("#dialog-map").dialog({
            width: 380,
            height: 350,
            autoOpen: false,
            //modal: true,
            resizable: true,
            buttons: {
                "닫기": function () {
                    $("#google-map").css("display", "none");
                    $(this).dialog("close");
                }
            }
        });
        $("#dialog-map").dialog("open");
        $("#google-map").css("display", "block");
        $("#google-map").html("");
        mw_google_map("google-map", $("#wr_google_map").val());
    }
    </script>

    <div id="dialog-map" class="dialog-content">
        <div id="google-map" style="border:1px solid #999; width:300px; height:200px; margin:20px; display:none;"></div>
    </div>
 
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<?  if ($mw_basic[cf_attribute] == 'qna' && $mw_basic[cf_qna_point_use]) { ?>
<tr>
<td class=mw_basic_write_title>· 질문 포인트</td>
<td class=mw_basic_write_content>
    <input type="text" size="5" class="ed" name="wr_qna_point" required numeric value="<?=$write[wr_qna_point]?>" itemname="질문 포인트"
    <? if (!$is_admin && $w == 'u') echo "disabled"; ?> > 포인트.
    질문자 포인트(<?=$member[mb_point]?>)에서 차감 (<?=$mw_basic[cf_qna_point_min]?>~<?=$mw_basic[cf_qna_point_max]?>점, 채택자 <?=$mw_basic[cf_qna_save]?>% 적립, 수정불가)
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>
<? if ($mw_basic[cf_attribute] == 'qna' && $is_admin) { ?>
<tr>
<td class=mw_basic_write_title>· 질문 상태</td>
<td class=mw_basic_write_content>
    <select name="wr_qna_status">
        <option value=""> </option>
        <option value="0"> 미해결 </option>
        <option value="1"> 해결 </option>
        <option value="2"> 보류 </option>
    </select>
    (관리자 전용)
    <script type="text/javascript"> document.fwrite.wr_qna_status.value = "<?=$write[wr_qna_status]?>"; </script>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<?  if ($mw_basic[cf_read_level] && $mw_basic[cf_read_level_own] <= $member[mb_level]) { ?>
<tr>
<td class=mw_basic_write_title>· 글읽기 레벨</td>
<td class=mw_basic_write_content>
    <select name="wr_read_level" class="mw_basic_text" itemname="글읽기 레벨">
    <option value=""> </option>
    <? for ($i=1; $i<=$member[mb_level]; $i++) { ?>
    <option value="<?=$i?>"> <?=$i?> </option>
    <? } ?>
    </select>
    <script type="text/javascript"> fwrite.wr_read_level.value = "<?=$write[wr_read_level]?>"; </script>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_kcb_post] && $mw_basic[cf_kcb_post_level] <= $member[mb_level]) { ?>
<tr>
<td class=mw_basic_write_title>· <? echo $mw_basic[cf_kcb_typ] == "okname" ? "실명인증" : "성인인증" ?> </td>
<td class=mw_basic_write_content>
    <input type=checkbox name=wr_kcb_use value=1> 사용
    <script type="text/javascript"> document.fwrite.wr_kcb_use.checked = "<?=$write[wr_kcb_use]?>" </script>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<?
if ($mw_basic[cf_reward]) {
    $reward = "";
    if ($w == "u") {
        $reward = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '$wr_id'");
        if ($reward) {
            if ($reward[re_edate] == "0000-00-00")
                $reward[re_edate] = date("Y-m-d", strtotime("+30 day", $g4[server_time]));
        }
    }
?>
<tr>
<td class=mw_basic_write_title>· 리워드 종류</td>
<td class=mw_basic_write_content>
    <select name="re_site" class="mw_basic_text" required itemname="리워드 종류">
    <option value=""> </option>
    <option value="linkprice"> 링크프라이스 </option>
    <option value="ilikeclick"> 아이라이크클릭 </option>
    </select>
    <script type="text/javascript"> fwrite.re_site.value = "<?=$reward[re_site]?>"; </script>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
<td class=mw_basic_write_title>· 리워드 주소</td>
<td class=mw_basic_write_content>
    <input type="text" size="50" name="re_url" value="<?=$reward[re_url]?>" class=mw_basic_text required itemname="리워드 주소">
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
<td class=mw_basic_write_title>· 리워드 종료</td>
<td class=mw_basic_write_content>
    <input type="text" id="re_edate" name="re_edate" class=mw_basic_text size="10" value="<?=$reward[re_edate]?>" readonly itemname="리워드 종료일" required>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
<td class=mw_basic_write_title>· 리워드 적립</td>
<td class=mw_basic_write_content>
    <input type="text" size="10" name="re_point" value="<?=$reward[re_point]?>" itemname="리워드 포인트" numeric required class=mw_basic_text> 포인트
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? if ($w == "u") { ?>
<tr>
<td class=mw_basic_write_title>· 리워드 상태</td>
<td class=mw_basic_write_content>
    <select name="re_status" class="mw_basic_text" itemname="리워드 상태">
    <option value=""> 종료 </option>
    <option value="1"> 진행중 </option>
    </select>
    <script type="text/javascript"> fwrite.re_status.value = "<?=$reward[re_status]?>"; </script></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>
<? } ?>

<?
if ($mw_basic[cf_vote] && $mw_basic[cf_vote_level] <= $member[mb_level]) { 
    $sql = "select * from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$wr_id'";
    $vote = sql_fetch($sql);

    if ($vote[vt_sdate] == "0000-00-00 00:00:00" || !$vote[vt_sdate]) {
        $vote[vt_sdate] = "";
        $vote[vt_stime] = "00";
    } else { 
        $vote[vt_stime] = date("H", strtotime($vote[vt_sdate]));
        $vote[vt_sdate] = date("Y-m-d", strtotime($vote[vt_sdate]));
    }
    if ($vote[vt_edate] == "0000-00-00 00:00:00" || !$vote[vt_edate]) {
        $vote[vt_edate] = "";
        $vote[vt_etime] = "00";
    } else { 
        $vote[vt_etime] = date("H", strtotime($vote[vt_edate]));
        $vote[vt_edate] = date("Y-m-d", strtotime($vote[vt_edate]));
    }
?>
<tr>
<td class=mw_basic_write_title>· 설문기간 </td>
<td class=mw_basic_write_content>
    <input type="text" id="vt_sdate" name="vt_sdate" class=mw_basic_text size="10" value="<?=$vote[vt_sdate]?>" readonly>
    <select name="vt_stime">
        <? for ($i=0; $i<=23; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?>
        <? } ?>
    </select> 시 ~
    <input type="text" id="vt_edate" name="vt_edate" class=mw_basic_text size="10" value="<?=$vote[vt_edate]?>" readonly>
    <select name="vt_etime">
        <? for ($i=0; $i<=23; $i++) { ?>
        <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?>
        <? } ?>
    </select> 시
    (비워두면 글작성시 부터 무제한)
    <script type="text/javascript">
    document.fwrite.vt_stime.value = "<?=$vote[vt_stime]?>";
    document.fwrite.vt_etime.value = "<?=$vote[vt_etime]?>";
    </script>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
<td class=mw_basic_write_title>· 설문다중선택 </td>
<td class=mw_basic_write_content>
    <select id="vt_multi" name="vt_multi">
    <option value="0"> 허용안함 </option>
    <? for ($i=2; $i<=10; $i++) { ?>
    <option value="<?=$i?>"> <?=$i?> 개까지 복수선택 </option>
    <? } ?>
    </select>
    <? if (!$vote[vt_multi]) $vote[vt_multi] = 0; ?>
    <script type="text/javascript"> fwrite.vt_multi.value = "<?=$vote[vt_multi]?>" </script>
</td>
</tr>
<? if ($is_admin == "super") { ?>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
<td class=mw_basic_write_title>· 설문포인트 </td>
<td class=mw_basic_write_content>
    <input type="text" id="vt_point" name="vt_point" class=mw_basic_text size="10" value="<?=$vote[vt_point]?>">
    (설문참여자에게 포인트를 지급합니다. 관리자 전용기능)
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>
<tr>
<td class=mw_basic_write_title>· 설문항목
        <span onclick="add_vote();" style='cursor:pointer; font-family:tahoma; font-size:12pt;'>+</span>
</td>
<td class=mw_basic_write_content>

<div id="mw_vote"></div>

<script type="text/javascript">
function add_vote(val) {
    if (!val) val = "";
    $("#mw_vote").append("<div style='margin:2px 0 2px 0;'><input type='text' size='50' name='vt_item[]' value=\""+val+"\" class=mw_basic_text></div>");
}
<?
if ($w == "") { 
    echo " add_vote(''); add_vote(); add_vote();";
} else  {
    $sql = "select * from $mw[vote_item_table] where vt_id = '$vote[vt_id]' order by vt_num";
    $qry = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($qry); $i++) {
        echo "add_vote('".addslashes(trim($row[vt_item]))."');\n";
    }

    if (!$i)
        echo " add_vote(''); add_vote(); add_vote();";
}
?>
</script>

</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_related]) { ?>
<tr>
<td class=mw_basic_write_title>· 관련글 키워드</td>
<td class=mw_basic_write_content height=50>
    <input type="text" size=50 name="wr_related" itemname="관련글 키워드" value="<?=$write[wr_related]?>" class=mw_basic_text> <br/>
    키워드를 , 컴마로 구분하여 입력해주세요. (예 : 한예슬, 얼짱, 몸짱)
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_comment_ban] && $mw_basic[cf_comment_ban_level] <= $member[mb_level]) { ?>
<tr>
<td class=mw_basic_write_title>· 코멘트 금지</td>
<td class=mw_basic_write_content>
    <input type=checkbox name=wr_comment_ban value=1> (코멘트를 원하지 않을 경우 체크해주세요.)
    <script type="text/javascript"> document.fwrite.wr_comment_ban.checked = "<?=$write[wr_comment_ban]?>" </script>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_ccl]) { ?>
<tr>
<td class=mw_basic_write_title>· CCL</td>
<td class=mw_basic_write_content>
    <select name="wr_ccl_by"><option value="">사용안함</option><option value="by">사용</option></select>
    영리목적 : <select name="wr_ccl_nc"><option value="nc">사용불가</option><option value="">사용가능</option></select>
    변경 : <select name="wr_ccl_nd"><option value="nd">변경불가</option><option value="sa">동일조건변경가능</option><option value="">변경가능</option></select>
    <a href="http://www.creativecommons.or.kr/info/about" target=_blank>CCL이란?</a>
    <? if ($w == "u") {?>
    <script type="text/javascript">
    document.fwrite.wr_ccl_by.value = "<?=$write[wr_ccl][by]?>";
    document.fwrite.wr_ccl_nc.value = "<?=$write[wr_ccl][nc]?>";
    document.fwrite.wr_ccl_nd.value = "<?=$write[wr_ccl][nd]?>";
    </script>
    <? } ?>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_link) { ?>
<? for ($i=1; $i<=$g4[link_count]; $i++) { ?>
<tr>
<td class=mw_basic_write_title>· 링크 #<?=$i?></td>
<td class=mw_basic_write_content>
    <input type="text" size=50 name="wr_link<?=$i?>" itemname="링크 #<?=$i?>" value="<?=$write["wr_link{$i}"]?>" class=mw_basic_text>
    <? if ($mw_basic[cf_link_target_level] && $mw_basic[cf_link_target_level] <= $member[mb_level]) { ?>
        <select name="wr_link<?=$i?>_target">
            <option value="_blank">새창 (_blank)</option>
            <option value="_self">현재창 (_self)</option>
        </select>
        <script type="text/javascript"> fwrite.wr_link<?=$i?>_target.value = "<?=$write["wr_link{$i}_target"]?>"; </script>
    <? } ?>
    <? if ($mw_basic[cf_link_write] && $mw_basic[cf_link_write] <= $member[mb_level] && $i == 1) { ?>
        <input type="checkbox" name="wr_link_write" value="1" <? if ($write[wr_link_write]) echo "checked"; ?> >
         본문 출력 없이 링크로 바로 이동
    <? } ?>
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>
<? } ?>

<? if ($mw_basic[cf_zzal]) { ?>
<tr>
<td class=mw_basic_write_title>· 짤방 이름</td>
<td class=mw_basic_write_content><input type="text" size=30 name="wr_zzal" itemname="짤방이름" value="<?=$write[wr_zzal]?>" <? if ($mw_basic[cf_zzal_must]) echo "required"; ?> class=mw_basic_text></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<!-- SWF업로더 시작-->
<? if ($mw_basic[cf_guploader] == '2') { ?>
<tr>
<td class=mw_basic_write_title>· SWF 멀티업로더 </td>
<td class=mw_basic_write_content><? include_once("$board_skin_path/mw.swfupload/index.php"); ?></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>
<!-- SWF업로더 끝 -->

<!-- 지업로더 시작-->
<? if ($mw_basic[cf_guploader] == '1') { ?>
<tr>
<td class=mw_basic_write_title> G 멀티업로더 </td>
<td class=mw_basic_write_content>
    <script type="text/javascript" src="<?=$board_skin_path?>/mw.guploader/guploader.js"></script>
    <script type="text/javascript">
    var uploader = new guploader('uploader');

    uploader.set_path("<?=$board_skin_path?>/mw.guploader");

    // 업로드 파일 갯수 제한
    uploader.set_file_list_limit("<?=$board[bo_upload_count]?>");

    // 업로드 용량 제한
    uploader.set_file_size_limit("<?=$board[bo_upload_size]?>");

    // 업로드 경로
    <? if ($wr_id) { ?>
    uploader.set_file_path("<?="$g4[path]/data/file/$bo_table"?>");
    <? } else { ?>
    uploader.set_file_path("<?="$g4[path]/data/guploader"?>");
    <? } ?>

    // 업로드 파일경로
    uploader.set_file_action("<?=$g4[url]?>/skin/board/<?=$board[bo_skin]?>/mw.guploader/guploader.php?bo_table=<?=$bo_table?>&mb_id=<?=$member[mb_id]?>&wr_id=<?=$wr_id?>&w=<?=$w?>");

    // 업로드 완료 후 실행할 함수
    uploader.set_after_upload("uploader.after_upload");

    // 업로드 완료 후 실행할 함수의 인수
    uploader.set_after_upload_val("bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&w=<?=$w?>");

    // 파일 삭제 함수 정의
    uploader.set_delete_file("uploader.delete_file");

    // 다운로드 실행 파일
    uploader.set_download_file("");

    // 이미지 가로사이즈 제한
    uploader.set_image_width("");

    // 업로더 실행
    uploader.draw_uploader();
    </script>
    <? } ?>
    <!-- 지업로더 끝 -->
</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>

<? if ($is_file && !($mw_basic[cf_guploader] && $is_member)) { ?>
<tr>
    <td class=mw_basic_write_title>
        <table><tr><td style=" padding-top: 10px;">
        · <? if ($mw_basic[cf_zzal]) echo "짤방"; else echo "파일"; ?>
        <span onclick="add_file();" style='cursor:pointer; font-family:tahoma; font-size:12pt;'>+</span>
        <span onclick="del_file();" style='cursor:pointer; font-family:tahoma; font-size:12pt;'>-</span>
        </td></tr></table>
    </td>
    <td class=mw_basic_write_content>
        <table id="variableFiles"></table><?// print_r2($file); ?>
	<? if ($mw_basic[cf_img_1_noview]) { ?>
	첫번째 첨부파일은 썸네일로만 출력됩니다. 본문에 출력되지 않습니다. 
        <? } else if ($mw_basic[cf_zzal] && $mw_basic[cf_zzal_must]) { ?>
        반드시 첫번째에 짤방 이미지를 첨부하셔야 합니다.
        <? } ?>
        <script type="text/javascript">
        var flen = 0;
        function add_file(delete_code)
        {
            var upload_count = <?=(int)$board[bo_upload_count]?>;
            if (upload_count && flen >= upload_count)
            {
                alert("이 게시판은 "+upload_count+"개 까지만 파일 업로드가 가능합니다.");
                return;
            }

            var objTbl;
            var objRow;
            var objCell;
            if (document.getElementById)
                objTbl = document.getElementById("variableFiles");
            else
                objTbl = document.all["variableFiles"];

            objRow = objTbl.insertRow(objTbl.rows.length);
            objCell = objRow.insertCell(0);

            objCell.innerHTML = "<input type='file' id=bf_file_" + flen + " name='bf_file[]' title='파일 용량 <?=$upload_max_filesize?> 이하만 업로드 가능' class=mw_basic_text>";

	    /*
	    str = "<input type='file' id=bf_file_" + flen + " name='bf_file[]' title='파일 용량 <?=$upload_max_filesize?> 이하만 업로드 가능' class=mw_basic_text> ";
	    str+= " <input type='button' value='본문에 넣기' onclick=\"document.getElementById('wr_content').value += '{이미지:" + flen + "}'\"";
	    objCell.innerHTML = str;
	    */

            if (delete_code)
                objCell.innerHTML += delete_code;
            else
            {
                <? if ($is_file_content) { ?>
                objCell.innerHTML += "<br><input type='text' size=50 name='bf_content[]' title='업로드 이미지 파일에 해당 되는 내용을 입력하세요.' class=mw_basic_text>";
                <? } ?>
                ;
            }

            flen++;
        }

        <?=$file_script; //수정시에 필요한 스크립트?>

        function del_file()
        {
            // file_length 이하로는 필드가 삭제되지 않아야 합니다.
            var file_length = <?=(int)$file_length?>;
            var objTbl = document.getElementById("variableFiles");
            if (objTbl.rows.length - 1 > file_length)
            {
                objTbl.deleteRow(objTbl.rows.length - 1);
                flen--;
            }
        }
        </script></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_trackback) { ?>
<tr>
    <td class=mw_basic_write_title>· 트랙백주소</td>
    <td class=mw_basic_write_content><input type="text" size=50 name=wr_trackback itemname="트랙백" value="<?=$trackback?>" class=mw_basic_text>
        <? if ($w=="u") { ?><input type=checkbox name="re_trackback" value="1">핑 보냄<? } ?></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($mw_basic[cf_change_image_size] && $member[mb_level] >= $mw_basic[cf_change_image_size_level]) { ?>
<tr>
    <td class=mw_basic_write_title>· 크기변경</td>
    <td class=mw_basic_write_content>
        <input type="text" size=5 name=change_image_size itemname="첨부이미지 크기변경" value="<?=$change_image_size?>" class=mw_basic_text>px
        (첨부이미지 크기를 변경합니다, 작게만 가능) 
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>


<? if ($is_norobot) { ?>
<tr>
    <td class=mw_basic_write_title>
        <?
        // 이미지 생성이 가능한 경우 자동등록체크코드를 이미지로 만든다.
        if (function_exists("imagecreate") && $mw_basic[cf_norobot_image]) {
            echo "<img src='$g4[bbs_path]/norobot_image.php?{$g4['server_time']}' border='0' align=absmiddle>";
            $norobot_msg = "* 왼쪽의 자동등록방지 코드를 입력하세요.";
        }
        else {
            echo $norobot_str;
            $norobot_msg = "* 왼쪽의 글자중 <FONT COLOR='red'>빨간글자</font>만 순서대로 입력하세요.";
        }
        ?>
    </td>
    <td><input type=input size=10 name=wr_key itemname="자동등록방지" required class=mw_basic_text>
        <span class=mw_basic_norobot><?=$norobot_msg?></span>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if (file_exists("$g4[bbs_path]/kcaptcha_session.php") && $is_guest) { ?>
<tr>
    <td class=write_head><img id='kcaptcha_image' /></td>
    <td><input class='ed' type=input size=10 name=wr_key itemname="자동등록방지" required>&nbsp;&nbsp;왼쪽의 글자를 입력하세요.</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? /* ?>
<script type="text/javascript"> var md5_norobot_key = ''; </script>
<tr>
    <td class=write_head><img id='kcaptcha_image' border='0' width=120 height=60 onclick="imageClick();" style="cursor:pointer;" title="글자가 잘안보이는 경우 클릭하시면 새로운 글자가 나옵니다."></td>
    <td><input class='ed' type=input size=10 name=wr_key itemname="자동등록방지" required>&nbsp;&nbsp;왼쪽의 글자를 입력하세요.</td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? */ ?>
<? } ?>


<tr><td colspan=2 height=1 class=mw_basic_line_color></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="100%" height="30">&nbsp;</td>
</tr>
<tr>
    <td width="100%" align="center" valign="top">
        <input type=image id="btn_submit" src="<?=$board_skin_path?>/img/btn_save.gif" border=0 accesskey='s'>&nbsp;
        <a href="./board.php?bo_table=<?=$bo_table?>"><img id="btn_list" src="<?=$board_skin_path?>/img/btn_list.gif" border=0></a></td>
</tr>
</table>
</form>

<? @include_once($mw_basic[cf_include_tail]); ?>

</td></tr></table>

<script type="text/javascript" src="<?="$g4[path]/js/jquery.kcaptcha.js"?>"></script>
<script type="text/javascript">

$(document).ready(function () {
    if (typeof(document.fwrite.ca_name) != 'undefined') {
        fwrite.ca_name.value = "<?=$sca?>";
    }

    <? /*if (!$is_member) { ?>$(imageClick);<? }*/ ?>

    <?
    // 관리자라면 분류 선택에 '공지' 옵션을 추가함
    if ($is_admin && !$mw_basic[cf_category_radio])
    {
        echo "
        if (typeof(document.fwrite.ca_name) != 'undefined')
        {
            document.fwrite.ca_name.options.length += 1;
            document.fwrite.ca_name.options[document.fwrite.ca_name.options.length-1].value = '공지';
            document.fwrite.ca_name.options[document.fwrite.ca_name.options.length-1].text = '공지';
        }";
    }
    ?>

    with (document.fwrite) {
        if (typeof(wr_name) != "undefined")
            wr_name.focus();
        else if (typeof(wr_subject) != "undefined")
            wr_subject.focus();
        else if (typeof(wr_content) != "undefined")
            wr_content.focus();

        if (typeof(ca_name) != "undefined") {
            <? if (!$mw_basic[cf_category_radio]) { ?>
                if (w.value == "u")
                    ca_name.value = "<?=$write[ca_name]?>";
            <? } else { ?>
                for (i=0; i<ca_name.length; i++) {
                    if (ca_name[i].value == "<?=urldecode($sca)?>")
                        ca_name[i].checked = true;
                }
            <? } ?>
        }
    } 


    <? if ($w == "" && $is_member) { ?>
    $.post("<?=$board_skin_path?>/mw.proc/mw.temp.php", 
        {
            "w": "get",
            "bo_table": "<?=$bo_table?>"
        },
        function(data) {
            data = data.split("-mw-basic-temp-return-");
            if (data[1] && confirm("임시 저장한 내용이 있습니다.\n\n불러오시겠습니까?")) {
                $("#wr_subject").val(data[0]);
                $("#wr_content").val(data[1]);
                <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") { ?>
                document.getElementById('tx_wr_content').value = data[1];
                ed_wr_content.resetDoc();
                <? } else if ($is_dhtml_editor && $mw_basic[cf_editor] == "geditor") { ?>
                geditor_wr_content.update();
                <? } ?>
            }
        }
    );
    //$("#wr_content").focusout(function () { setTimeout(mw_save_temp, 100); });
    //$("#wr_subject").focusout(function () { setTimeout(mw_save_temp, 100); });
    <? } ?>

    window.onbeforeunload = function () {
        <? if ($w == "") echo "mw_save_temp();"; ?>
        return "다른 페이지로 이동하시면 작성중인 글이 모두 사라집니다.";
    }
});

function mw_save_temp(msg)
{
    <? if (!$is_member) echo "return;"; ?>
    var wr_subject = $("#wr_subject").val();
    var wr_content = $("#wr_content").val();

    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") { ?>
    wr_content = ed_wr_content.outputBodyHTML();
    <? } ?>

    $.get("<?=$board_skin_path?>/mw.proc/mw.temp.php", {
        "w" : "save",
        "bo_table" : "<?=$bo_table?>",
        "wr_subject" : encodeURIComponent(wr_subject),
        "wr_content" : encodeURIComponent(wr_content)
    }, function () {
        if (msg) alert(msg);
    });
}

function html_auto_br(obj) {
    if (obj.checked) {
        result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
        if (result)
            obj.value = "html2";
        else
            obj.value = "html1";
    }
    else
        obj.value = "";
}

function fwrite_check(f) {

    <? if ($is_category && $mw_basic[cf_category_radio]) { ?>
    is_cate = false;
    for (i=0; i<f.ca_name.length; i++) {
        if (f.ca_name[i].checked == true) {
            is_cate = true;
        }
    }
    if (!is_cate) {
        alert("분류를 선택해주세요.");
        return false;
    }
    <? } ?>
    /*
    var s = "";
    if (s = word_filter_check(f.wr_subject.value)) {
        alert("제목에 금지단어('"+s+"')가 포함되어있습니다");
        return false;
    }

    if (s = word_filter_check(f.wr_content.value)) {
        alert("내용에 금지단어('"+s+"')가 포함되어있습니다");
        return false;
    }
    */

    if (document.getElementById('char_count')) {
        if (char_min > 0 || char_max > 0) {
            var cnt = parseInt(document.getElementById('char_count').innerHTML);
            if (char_min > 0 && char_min > cnt) {
                alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                return false;
            }
            else if (char_max > 0 && char_max < cnt) {
                alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                return false;
            }
        }
    }

    if (document.getElementById('tx_wr_content')) {
        if (!ed_wr_content.outputBodyHTML()) { 
            alert('내용을 입력하십시오.'); 
            ed_wr_content.returnFalse();
            return false;
        }
    }

    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") echo cheditor3('wr_content'); ?>

    var subject = "";
    var content = "";
    <? if (!$is_admin) { ?>
    $.ajax({
        url: "<?=$board_skin_path?>/ajax.filter.php",
        type: "POST",
        data: {
            "subject": f.wr_subject.value,
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });
    <? } ?>

    if (subject) {
        alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
        f.wr_subject.focus();
        return false;
    }

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        if (typeof(ed_wr_content) != "undefined") 
            ed_wr_content.returnFalse();
        else 
            f.wr_content.focus();
        return false;
    }

    if (!check_kcaptcha(f.wr_key)) {
        return false;
    }

    /*if (typeof(f.wr_key) != 'undefined') {
        if (hex_md5(f.wr_key.value) != md5_norobot_key) {
            alert('자동등록방지용 글자가 제대로 입력되지 않았습니다.');
            f.wr_key.select();
            f.wr_key.focus();
            return false;
        }
    }*/

    <? if ($mw_basic[cf_zzal] && $mw_basic[cf_zzal_must]) { ?>
    var zzal = document.getElementById("bf_file_0").value;
    if (f.w.value=='' && !zzal)
    {
        alert("짤방 이미지를 입력해 주세요.");
        return false;
    }

    if (f.w.value=='' && !zzal.match(/.(gif|jpg|jpeg|png)$/i))
    {
        alert(document.getElementById("bf_file[]").value + ' 은(는) 이미지 파일이 아닙니다.');
        return false;
    }
    <? } ?>

    <? if ($mw_basic[cf_bomb_level] && $mw_basic[cf_bomb_level] <= $member[mb_level]) { ?>
    if (fwrite.bm_year.value || fwrite.bm_month.value || fwrite.bm_day.value || fwrite.bm_hour.value || fwrite.bm_minute.value)
    {
        bomb_day = fwrite.bm_year.value + '-' + fwrite.bm_month.value + '-' + fwrite.bm_day.value + ' ' + fwrite.bm_hour.value + ':' + fwrite.bm_minute.value;
        bomb_error = '';
        $.ajax({
            url: "<?=$board_skin_path?>/mw.proc/mw.bomb.days.check.php",
            type: "POST",
            data: {
                'bo_table':'<?=$bo_table?>',
                'bomb_day':bomb_day
            },
            async: false,
            cache: false,
            success: function(data, textStatus) {
                bomb_error = data;
            }
        });
        if (bomb_error) {
            alert(bomb_error);
            return false;
        }
    }
    <? } ?>

    var geditor_status = document.getElementById("geditor_wr_content_geditor_status");
    if (geditor_status != null) {
        if (geditor_status.value == "TEXT") {
            f.html.value = "html2";
        }
        else if (geditor_status.value == "WYSIWYG") {
            f.html.value = "html1";
        }
    }

    //document.getElementById('btn_submit').disabled = true;
    $("#btn_submit").attr("src", "<?=$board_skin_path?>/img/icon_loading.gif");
    document.getElementById('btn_list').disabled = true;

    <?
    if ($g4[https_url])
        echo "f.action = '$g4[https_url]/$g4[bbs]/write_update.php';";
    else
        echo "f.action = './write_update.php';";
    ?>
 
    window.onbeforeunload = function () { mw_save_temp(); }
    //f.submit();
}
</script>

<? if ($is_admin == "super") { ?>
<script type="text/javascript">
function mw_config() {
    win_open("<?=$board_skin_path?>/mw.adm/mw.config.php?bo_table=<?=$bo_table?>", "config", "width=980, height=700, scrollbars=yes");
}
</script>
<? } ?>

<script type="text/javascript" src="<?="$g4[path]/js/board.js"?>"></script>

<? if ($is_dhtml_editor && $mw_basic[cf_editor] == "geditor") { ?>
    <script type="text/javascript"> var g4_skin_path = "<?=$board_skin_path?>"; </script>
    <script type="text/javascript" src="<?=$board_skin_path?>/mw.geditor/geditor.js"></script>
    <? if (strstr($write[wr_option], "html2")) { ?>
	<script type="text/javascript"> geditor_wr_content.mode_change(); </script>
    <? } ?>
<? } ?>

<style type="text/css">
<?=$mw_basic[cf_css]?>
</style>


<?  } // 실명인증 ?>
