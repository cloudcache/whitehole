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

define('SECRET_COMMENT', "비밀글 입니다.");

// 실명인증 & 성인인증
if (($mw_basic[cf_kcb_read] || $write[wr_kcb_use]) && !is_okname()) {
    check_okname();
} else {

if (!is_array($mw_membership)) {
    $mw_membership = array();
    $mw_membership_icon = array();
}

$write_error = '';
if (!$is_member && !$is_comment_write && $mw_basic[cf_comment_write]) {
    $write_error = "readonly onclick=\"alert('로그인 하신 후 코멘트를 작성하실 수 있습니다.'); return false;\"";
}

if ($mw_basic[cf_kcb_comment] && !is_okname()) {
    $is_comment_write = false;
    $write_error = '';
}

if ($is_comment_write) {
    if ($mw_basic[cf_comment_ban] && $write[wr_comment_ban]) {
        $is_comment_write = false;
    }
}

if ($mw_basic[cf_must_notice_comment]) {
    $tmp_notice = str_replace("\n", ",", trim($board[bo_notice]));
    $cnt_notice = sizeof(explode(",", $tmp_notice));

    if ($tmp_notice) {
        $sql = "select count(*) as cnt from $mw[must_notice_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and wr_id in ($tmp_notice)";
        $row = sql_fetch($sql);
        if ($row[cnt] != $cnt_notice) {
            $is_comment_write = false;
            $write_error = "readonly onclick=\"alert('$board[bo_subject] 게시판의 공지를 모두 읽으셔야 코멘트를 작성하실 수 있습니다.'); return false;\"";
        }
            //alert("$board[bo_subject] 공지를 모두 읽으셔야 글읽기가 가능합니다.");
    }
}

if ($mw_basic[cf_comment_write_count]) {
    $sql = " select count(*) as cnt from $write_table where wr_num = '$write[wr_num]' and wr_is_comment = '1' ";
    if ($board[bo_comment_level] == 1 && !$is_member)
        $sql.= " and wr_ip = '$_SERVER[REMOTE_ADDR]' ";
    else
        $sql.= " and mb_id = '$member[mb_id]' ";

    $tmp = sql_fetch($sql);
    if ($tmp[cnt] >= $mw_basic[cf_comment_write_count]) {
        $is_comment_write = false;
        $write_error = "readonly onclick=\"alert('게시물당 코멘트를  {$mw_basic[cf_comment_write_count]}번만 작성하실 수 있습니다.'); return false;\"";
    }
}

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership")) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_comment");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        $is_comment_write = false;
}

if ($cwin==1) {
    echo "<link rel='stylesheet' href='$board_skin_path/style.common.css' type='text/css'>";
    echo "<style type='text/css'> #mw_basic { width:98%; padding:10px; } </style>";
    echo "<div id=mw_basic>";
}

if (!$is_admin && $write[wr_view_block] && $cwin)
    alert("이 게시물 보기는 차단되었습니다. 관리자만 접근 가능합니다.");

// 코멘트 작성 기간
if ($mw_basic[cf_comment_period] > 0) {
    if ($g4[server_time] - strtotime($write[wr_datetime]) > 60*60*24*$mw_basic[cf_comment_period]) {
        if ($mw_basic[cf_comment_default]) $mw_basic[cf_comment_default] .= "\n";
        $mw_basic[cf_comment_default] .= "작성한지 $mw_basic[cf_comment_period]일이 지난 게시물에는 코멘트를 작성할 수 없습니다.";
    }
}

?>

<? if ($mw_basic[cf_source_copy] && $cwin) { // 출처 자동 복사 ?>
<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/autosourcing.open.compact.js"></script>
<style type="text/css">
DIV.autosourcing-stub { display:none }
DIV.autosourcing-stub-extra { position:absolute; opacity:0 }
</style>
<script type="text/javascript">
AutoSourcing.setTemplate("<p style='margin:11px 0 7px 0;padding:0'> <a href='{link}' target='_blank'> [출처] {title} - {link}</a> </p>");
AutoSourcing.setString(<?=$wr_id?> ,"<?=$config[cf_title];//$view[wr_subject]?>", "<?=$view[wr_name]?>", "<?=$copy_url?>");
AutoSourcing.init( 'view_%id%' , true);
</script>
<? } ?>

<script type="text/javascript">
// 글자수 제한
var char_min = parseInt(<?=$comment_min?>); // 최소
var char_max = parseInt(<?=$comment_max?>); // 최대
</script>

<? if ($cwin==1) { ?>
<script type="text/javascript" src="<?="$board_skin_path/mw.js/mw_image_window.js"?>"></script>
<table width=100% cellpadding=10 align=center><tr><td>
<?}?>

<!-- 코멘트 리스트 -->
<div id="commentContents">

<? if ($mw_basic[cf_comment_notice]) { ?>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
    <td></td>
    <td width="100%">
        <table width=100% cellpadding=0 cellspacing=0>
        <tr>
            <!-- 이름, 아이피 -->
            <td>
                <div class=mw_basic_comment_name><img src="<?=$board_skin_path?>/img/icon_notice.gif"></div>
            </td>
            <!-- 링크 버튼, 코멘트 작성시간 -->
            <td align=right>
                <!--
                <span class=mw_basic_comment_datetime><?=substr($view[wr_datetime],0,10)." (".get_yoil($view[wr_datetime]).") ".substr($view[wr_datetime],11,5)?></span>-->
            </td>
        </tr>
        </table>
        <table width=100% cellpadding=0 cellspacing=0 class=mw_basic_comment_notice>
        <tr>
            <td colspan=2>
                <div><?=mw_reg_str(get_text($mw_basic[cf_comment_notice], 1))?></div>
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>
<br/>

<? } ?>

<?
$is_comment_best = array();
if ($mw_basic[cf_comment_best]) {
    $sql = " select * from $write_table where wr_parent = '$wr_id' and wr_is_comment = '1' and wr_good > 0 ";
    if ($mw_basic[cf_comment_best_limit]) {
        $sql .= " and wr_good >= '$mw_basic[cf_comment_best_limit]' ";
    }
    $sql.= " order by wr_good desc, wr_datetime asc limit $mw_basic[cf_comment_best] ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {

    //$row = get_list($row, $board, $board_skin_path);
    //if ($is_admin == "super") echo "[$row[content]]";
    $is_comment_best[] = $row[wr_id];

    $tmp_name = get_text(cut_str($row[wr_name], $config[cf_cut_name])); // 설정된 자리수 만큼만 이름 출력
    if ($board[bo_use_sideview])
        $row[name] = get_sideview($row[mb_id], $tmp_name, $row[wr_email], $row[wr_homepage]);
    else
        $row[name] = "<span class='".($row[mb_id]?'member':'guest')."'>$tmp_name</span>";

    if ($mw_basic[cf_attribute] == "anonymous") $row[name] = mw_anonymous_nick($row[mb_id], $row[wr_ip]); 
    if ($row[wr_anonymous]) $row[name] = mw_anonymous_nick($row[mb_id], $row[wr_ip]); 

    $html = 0;
    if (strstr($row['wr_option'], "html1")) $html = 1;
    if (strstr($row['wr_option'], "html2")) $html = 2;

    $row[wr_content] = mw_tag_debug($row[wr_content]);
    $row[content] = $row[content1] = SECRET_COMMENT;
    if (!strstr($row[wr_option], "secret") ||
        $is_admin ||
        ($write[mb_id]==$member[mb_id] && $member[mb_id]) ||
        ($row[mb_id]==$member[mb_id] && $member[mb_id])) {
        $row[content1] = $row[wr_content];
        $row[content] = conv_content($row[wr_content], $html, 'wr_content');
        $row[content] = search_font($stx, $row[content]);
    }

    $row[trackback] = url_auto_link($row[wr_trackback]);
    $row[datetime] = substr($row[wr_datetime],2,14);

    $row[ip] = $row[wr_ip];
    if (!$is_admin)
        $row[ip] = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", "\\1.♡.\\3.\\4", $row[wr_ip]);
    else if ($row[mb_id] == $config[cf_admin])
        $row[ip] = "";

    $is_comment_image = false;
    $comment_image = "$board_skin_path/img/noimage.gif";
    if ($mw_basic[cf_attribute] != "anonymous" && !$row[wr_anonymous] && $row[mb_id] && file_exists("$comment_image_path/{$row[mb_id]}")) {
        $comment_image = "$comment_image_path/{$row[mb_id]}";
        $is_comment_image = true;
    }

    // 멤버쉽 아이콘
    if (function_exists("mw_cash_membership_icon") && $row[mb_id] != $config[cf_admin])
    {
        if (!in_array($row[mb_id], $mw_membership)) {
            $mw_membership[] = $row[mb_id];
            $mw_membership_icon[$row[mb_id]] = mw_cash_membership_icon($row[mb_id]);
            $row[name] = $mw_membership_icon[$row[mb_id]].$row[name];
        } else {
            $row[name] = $mw_membership_icon[$row[mb_id]].$row[name];
        }
    }

    // 코멘트 첨부파일
    $file = get_comment_file($bo_table, $row[wr_id]);
    if ($file[0][view]) {
        if ($board[bo_image_width] < $file[0][image_width]) { // 이미지 크기 조절
            $img_width = $board[bo_image_width];
        } else {
            $img_width = $file[0][image_width];
        }
        $file[0][view] = str_replace("<img", "<img width=\"{$img_width}\"", $file[0][view]);

	if ($mw_basic[cf_exif]) {
	    $file[0][view] = str_replace("image_window(this)", "show_exif({$row[wr_id]}, this, event)", $file[0][view]);
	    $file[0][view] = str_replace("title=''", "title='클릭하면 메타데이터를 보실 수 있습니다.'", $file[0][view]);
	} else {
	    $file[0][view] = str_replace("onclick='image_window(this);'", 
		"onclick='mw_image_window(this, {$file[0][image_width]}, {$file[0][image_height]});'", $file[0][view]);
	    // 제나빌더용 (그누보드 원본수정으로 인해 따옴표' 가 없음;)
	    $file[0][view] = str_replace("onclick=image_window(this);", 
		"onclick='mw_image_window(this, {$file[0][image_width]}, {$file[0][image_height]});'", $file[0][view]); 
	}
        if ($row[content] != SECRET_COMMENT)
            $row[content] = $file[0][view] . "<br/><br/>" . $row[content];
    }

    // 가변 파일
    if ($file[0][source] && !$file[0][view]) {
        ob_start();
        ?>
        <div class="mw_basic_comment_download_file">
                <a href="javascript:file_download('<?=$file[0][href]?>', '<?=addslashes($file[0][source])?>', '<?=$i?>');"
                    title="<?=$file[0][content]?>"><img src="<?=$board_skin_path?>/img/icon_file_down.gif" align=absmiddle>&nbsp;<?=$file[0][source]?></a>
                <span class=mw_basic_view_file_info> (<?=$file[0][size]?>), Down : <?=$file[0][download]?>, <?=$file[0][datetime]?></span>
        </div>
        <?
        $comment_file = ob_get_contents();
        ob_end_clean();
        if ($row[content] != SECRET_COMMENT)
            $row[content] = $comment_file . "<br/>" . $row[content];

        $ss_name = "ss_view_{$bo_table}_{$row[wr_id]}";
        set_session($ss_name, TRUE);
    }

    $row[content] = mw_reg_str($row[content]); // 자동치환
    $row[name] = get_name_title($row[name], $row[wr_name]); // 호칭

    // BC코드
    $row[content] = bc_code($row[content]);
    $row[content] = mw_tag_debug($row[content]); // 잘못된 태그교정
?>

<div class="mw_basic_comment_best">
<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td valign="top" style="text-align:left;">
    <img src="<?=$comment_image?>" 
        style="width:58px; height:58px; border:3px solid #f2f2f2; margin:0 10px 5px 0;">
    <?
    if ($mw_basic[cf_icon_level]) { 
        $level = mw_get_level($row[mb_id]);
        echo "<div class=\"icon_level".($level+1)."\">&nbsp;</div>";
        $exp = $icon_level_mb_point[$row[mb_id]] - $level*$mw_basic[cf_icon_level_point];
        $per = round($exp/$mw_basic[cf_icon_level_point]*100);
        if ($per > 100) $per = 100;
        echo "<div class=\"level_exp_bg_{$row[mb_id]}\"><div class=\"level_exp_dot_{$row[mb_id]}\">&nbsp;</div></div>";
        echo "<style type=\"text/css\">
            .level_exp_bg_{$row[mb_id]} { background:url($board_skin_path/img/level_exp_bg.gif); width:64px; height:3px; font-size:1px; line-height:1px; margin:5px 0 0 0; }
            .level_exp_dot_{$row[mb_id]} { background:url($board_skin_path/img/level_exp_dot.gif); width:$per%; height:3px; }
        </style>";
    }
    ?>
</td>
<td width="2" bgcolor="#dedede"><div style="width:2px;"></div></td>
<td><div style="width:10px;"></div></td>

<td width="100%" valign="top">
    <table width=100% height="28" cellpadding=0 cellspacing=0 style="background:url(<?=$board_skin_path?>/img/co_title_bg.gif);">
    <tr>
        <!-- 이름, 아이피 -->
        <td>
            <img src="<?=$board_skin_path?>/img/comment_best.gif" align="absmiddle">
            <? if ($mw_basic[cf_attribute] == 'qna' && $write[wr_qna_id] == $row[wr_id]) { ?> <img src="<?=$board_skin_path?>/img/icon_choose.png" align="absmiddle"> <? } ?>
            <span class=mw_basic_comment_name><?=$row[name]?></span>
            <? if ($is_ip_view && $row[ip]) { ?> <span class=mw_basic_comment_ip>(<?=$row[ip]?>)</span> <?}?>
            <? if ($is_admin) { ?>
            <img src="<?=$board_skin_path?>/img/btn_intercept_small.gif" align=absmiddle title='접근차단' style="cursor:pointer" onclick="btn_intercept('<?=$row[mb_id]?>')">
            <img src="<?=$board_skin_path?>/img/btn_ip.gif" align=absmiddle title='IP조회' style="cursor:pointer" onclick="btn_ip('<?=$row[wr_ip]?>')">
            <img src="<?=$board_skin_path?>/img/btn_ip_search.gif" align=absmiddle title='IP검색' style="cursor:pointer" onclick="btn_ip_search('<?=$row[wr_ip]?>')">
            <? } ?>
            <span class=mw_basic_comment_datetime><?=substr($row[wr_datetime],0,10)." (".get_yoil($row[wr_datetime]).") ".substr($row[wr_datetime],11,5)?></span>

        </td>
        <!-- 링크 버튼, 코멘트 작성시간 -->
        <td align=right style="margin-right:10px;">
            <? if ($mw_basic[cf_comment_good]) { ?>
                <span class="mw_basic_comment_good"><a onclick="mw_comment_good(<?=$row[wr_id]?>, 'good')">추천</a>
                <span id="mw_comment_good_<?=$row[wr_id]?>"><?=$row[wr_good]?></span></span><? } ?>
            <? if ($mw_basic[cf_comment_nogood]) { ?>
                <span class="mw_basic_comment_nogood"><a onclick="mw_comment_good(<?=$row[wr_id]?>, 'nogood')">반대</a>
                <span id="mw_comment_nogood_<?=$row[wr_id]?>"><?=$row[wr_nogood]?></span></span><? } ?>
        </td>
    </tr>
    </table>

    <table width=100% cellpadding=0 cellspacing=0 class=mw_basic_comment_content>
    <tr>
        <td valign="top" style="background-color:#ffecd7;">
            <!-- 코멘트 출력 -->
            <div id=view_<?=$row[wr_id]?>_best>
            <?
            $str = $row[content];
            if (strstr($row[wr_option], "secret")) {
                $str = "<span class='mw_basic_comment_secret'>* $str</span>";
            }
            $str = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $str);
            $str = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(swf)\".*\<\/a\>\]/i", "<script>doc_write(flash_movie('$1://$2.$3'));</script>", $str);
            $str = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\"\s*[^\>]*\>[^\s]*\<\/a\>\]/i", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $str);

            $str = preg_replace_callback("/\[code\](.*)\[\/code\]/iU", "_preg_callback", $str);
            echo $str;
            ?>
            </div>
            <? if ($row[trackback]) { echo "<p>".$row[trackback]."</p>"; } ?>
            <? if ($mw_basic[cf_source_copy]) { // 출처 자동 복사 ?>
            <? $copy_url = set_http("{$g4[url]}/{$g4[bbs]}/board.php?bo_table={$bo_table}&wr_id={$wr_id}#c_{$row[wr_id]}"); ?>
            <script type="text/javascript">
            AutoSourcing.setString(<?=$row[wr_id]?> ,"<?=$config[cf_title]?>", "<?=$row[wr_name]?>", "<?=$copy_url?>");
            </script>
            <? } ?>
        </td>
    </tr>
    </table>
</tr>
<tr>
<td colspan="4" height="10"></td>
</tr>
</table>
</div>

<?  } } ?>

<a id="cs" name="cs"></a>
<? if ($is_admin) { ?> <input onclick="$('input[name=chk_comment_id[]]').attr('checked', this.checked);" type=checkbox> 코멘트 전체 선택 <? } ?>

<?
$total_count = count($list);

if ($mw_basic[cf_comment_page]) { // 코멘트 페이지
    $rows = $mw_basic[cf_comment_page_rows];;
    $total_page  = @ceil($total_count / $rows);  // 전체 페이지 계산
    if (!$total_page) $total_page = 1;
    
    if (!is_numeric($cpage)) { // 페이지가 없으면
        if ($board[bo_reply_order])
            $cpage = $total_page;
        else
            $cpage = 1;
    }
    if ($_c) { // 코멘트 페이지 찾아가기
        $t_rows = 1;
        $t_page = 1;
        for ($i=0, $m=sizeof($list); $i<$m; $i++) {
            if ($list[$i][wr_id] == $_c) {
                $cpage = $t_page;
            } else {
                if ($t_rows++ % $rows == 0) {
                    $t_page++;
                }
            }
        }
    }
    $from_record = ($cpage - 1) * $rows; // 시작 열을 구함  */
    $to_record = $cpage == $total_page ? $total_count : $rows * $cpage;

    //$qstr = preg_replace("/(\&page=.*)/", "", $qstr);
    $comment_pages = get_paging($config[cf_write_pages], $cpage, $total_page, "$PHP_SELF?bo_table=$bo_table&wr_id=$wr_id{$qstr}&cpage=");
    $comment_pages = preg_replace("/(\&cpage=[0-9]+)/", "$1#cs", $comment_pages);
    
} else {
    $from_record = 0;
    $to_record = $total_count;
}

for ($i=$from_record; $i<$to_record; $i++) {

    @include($mw_basic[cf_include_comment_main]);

    $list[$i][name] = get_name_title($list[$i][name], $list[$i][wr_name]);

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

    $html = 0;
    if (strstr($list[$i]['wr_option'], "html1")) $html = 1;
    if (strstr($list[$i]['wr_option'], "html2")) $html = 2;

    if ($html > 0) {
        $list[$i][wr_content] = mw_tag_debug($list[$i][wr_content]);
        $list[$i][content] = $list[$i][content1] = SECRET_COMMENT;
        if (!strstr($list[$i][wr_option], "secret") ||
            $is_admin ||
            ($write[mb_id]==$member[mb_id] && $member[mb_id]) ||
            ($list[$i][mb_id]==$member[mb_id] && $member[mb_id])) {
            $list[$i][content1] = $list[$i][wr_content];
            $list[$i][content] = conv_content($list[$i][wr_content], $html, 'wr_content');
            $list[$i][content] = search_font($stx, $list[$i][content]);
        }
    }

    // 코멘트 첨부파일
    $file = get_comment_file($bo_table, $list[$i][wr_id]);
    if ($file[0][view]) {
        if ($board[bo_image_width] < $file[0][image_width]) { // 이미지 크기 조절
            $img_width = $board[bo_image_width];
        } else {
            $img_width = $file[0][image_width];
        }
        $file[0][view] = str_replace("<img", "<img width=\"{$img_width}\"", $file[0][view]);

        if ($mw_basic[cf_exif]) {
            $file[0][view] = str_replace("image_window(this)", "show_exif({$list[$i][wr_id]}, this, event)", $file[0][view]);
            $file[0][view] = str_replace("title=''", "title='클릭하면 메타데이터를 보실 수 있습니다.'", $file[0][view]);
        } else {
            $file[0][view] = str_replace("onclick='image_window(this);'", 
                "onclick='mw_image_window(this, {$file[0][image_width]}, {$file[0][image_height]});'", $file[0][view]);
            // 제나빌더용 (그누보드 원본수정으로 인해 따옴표' 가 없음;)
            $file[0][view] = str_replace("onclick=image_window(this);", 
                "onclick='mw_image_window(this, {$file[0][image_width]}, {$file[0][image_height]});'", $file[0][view]); 
        }
        if ($list[$i][content] != SECRET_COMMENT)
            $list[$i][content] = $file[0][view] . "<br/><br/>" . $list[$i][content];
    }

    // 가변 파일
    if ($file[0][source] && !$file[0][view]) {
        ob_start();
        ?>
        <div class="mw_basic_comment_download_file">
                <a href="<?=$board_skin_path?>/mw.proc/mw.comment.download.php?bo_table=<?=$bo_table?>&wr_id=<?=$list[$i][wr_id]?>&bf_no=0"
                    title="<?=$file[0][content]?>"><img src="<?=$board_skin_path?>/img/icon_file_down.gif" align=absmiddle>&nbsp;<?=$file[0][source]?></a>
                <span class=mw_basic_view_file_info> (<?=$file[0][size]?>), Down : <?=$file[0][download]?>, <?=$file[0][datetime]?></span>
        </div>
        <?
        $comment_file = ob_get_contents();
        ob_end_clean();
        if ($list[$i][content] != SECRET_COMMENT)
            $list[$i][content] = $comment_file . "<br/>" . $list[$i][content];

        $ss_name = "ss_view_{$bo_table}_{$list[$i][wr_id]}";
        set_session($ss_name, TRUE);
    }

    if ($list[$i][wr_singo] && $list[$i][wr_singo] >= $mw_basic[cf_singo_number] && $mw_basic[cf_singo_write_block]) {
        $content = " <div class='singo_info'> 신고가 접수된 게시물입니다. (신고수 : {$list[$i][wr_singo]}회)<br/>";
        $content.= " <span onclick=\"btn_singo_view({$list[$i][wr_id]})\" class='btn_singo_block'>여기</span>를 클릭하시면 내용을 볼 수 있습니다.";
        if ($is_admin == "super")
            $content.= " <span class='btn_singo_block' onclick=\"btn_singo_clear({$list[$i][wr_id]})\">[신고 초기화]</span> ";
        $content.= " </div>";
        $content.= " <div id='singo_block_{$list[$i][wr_id]}' class='singo_block'> {$list[$i][content]} </div>";
        $list[$i][content] = $content;
    }

    $comment_id = $list[$i][wr_id];
    if ($mw_basic[cf_singo]) {
        $list[$i][singo_href] = "javascript:btn_singo($comment_id, $write[wr_parent])";
    }

    // 코멘트 비밀 리플 보이기
    if ($list[$i][content] == SECRET_COMMENT) {
        for ($j=$i-1; $j>=0; $j--) {
            if ($list[$j][wr_comment] == $list[$i][wr_comment] && $list[$j][wr_comment_reply] == substr($list[$i][wr_comment_reply], 0, strlen($list[$i][wr_comment_reply])-1)) {
                if (trim($list[$j][mb_id]) && $list[$j][mb_id] == $member[mb_id]) {
                    $list[$i][content] = conv_content($list[$i][wr_content], $html, 'wr_content');
                    $list[$i][content] = search_font($stx, $list[$i][content]);
                }
                break;
            }
        }
    }

    // 로그버튼
    $history_href = "";
    if ($mw_basic[cf_post_history] && $member[mb_level] >= $mw_basic[cf_post_history_level]) {
        $history_href = "javascript:btn_history({$list[$i][wr_id]})";
    }

    if ($mw_basic[cf_attribute] == "anonymous") $list[$i][name] = mw_anonymous_nick($list[$i][mb_id], $list[$i][wr_ip]); 
    if ($list[$i][wr_anonymous]) $list[$i][name] = mw_anonymous_nick($list[$i][mb_id], $list[$i][wr_ip]); 

    if (!$is_comment_write) {
        $list[$i][is_edit] = false;
        $list[$i][is_reply] = false;
    }

    $tmpsize = array(0, 0);
    $is_comment_image = false;
    $comment_image = "$board_skin_path/img/noimage.gif";
    if ($mw_basic[cf_attribute] != "anonymous" && !$list[$i][wr_anonymous] && $list[$i][mb_id] && file_exists("$comment_image_path/{$list[$i][mb_id]}")) {
        $comment_image = "$comment_image_path/{$list[$i][mb_id]}";
        $is_comment_image = true;
        $tmpsize = @getImageSize($comment_image);
    }

    $list[$i][content] = mw_reg_str($list[$i][content]); // 자동치환
    $list[$i][name] = get_name_title($list[$i][name], $list[$i][wr_name]); // 호칭

    $list[$i][content] = bc_code($list[$i][content]);
    $list[$i][content] = mw_tag_debug($list[$i][content]);

    // 관리자 게시물은 IP 주소를 보이지 않습니다
    if ($list[$i][mb_id] == $config[cf_admin]) $list[$i][ip] = "";

?>
<a name="c_<?=$comment_id?>"></a>

<table width=100% height="1" cellpadding=0 cellspacing=0 style="margin-bottom:5px;">
<tr>
    <td style="line-height:0;"><? for ($k=0; $k<strlen($list[$i][wr_comment_reply]); $k++) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
    <td width="100%" height="1" style="border-top:1px solid #ddd;"></td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
    <td><? for ($k=0; $k<strlen($list[$i][wr_comment_reply]); $k++) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>

    <td valign="top" style="text-align:left;">
        <img src="<?=$comment_image?>" class="comment_image" onclick="mw_image_window(this, <?=$tmpsize[0]?>, <?=$tmpsize[1]?>);">

        <? if (($is_member && $list[$i][mb_id] == $member[mb_id] && !$list[$i][wr_anonymous]) || $is_admin) { ?>
        <div style="margin:0 0 0 10px;"><a href="javascript:mw_member_photo('<?=$list[$i][mb_id]?>')"
            style="font:normal 11px 'gulim'; color:#888; text-decoration:none;"><? echo $is_comment_image ? "사진변경" : "사진등록"; ?></a></div>
        <? } ?>

        <script type="text/javascript">
        function mw_member_photo(mb_id) {
            win_open('<?=$board_skin_path?>/mw.proc/mw.comment.image.php?bo_table=<?=$bo_table?>&mb_id='+mb_id,'comment_image','width=500,height=350');
        }
        </script>

        <?
        if ($mw_basic[cf_icon_level] && !$list[$i][wr_anonymous] && $mw_basic[cf_attribute] != "anonymous") { 
            $level = mw_get_level($list[$i][mb_id]);
            echo "<div class=\"icon_level".($level+1)."\">&nbsp;</div>";
            $exp = $icon_level_mb_point[$list[$i][mb_id]] - $level*$mw_basic[cf_icon_level_point];
            $per = round($exp/$mw_basic[cf_icon_level_point]*100);
            if ($per > 100) $per = 100;
            echo "<div style=\"background:url($board_skin_path/img/level_exp_bg.gif); width:61px; height:3px; font-size:1px; line-height:1px; margin:5px 0 0 3px;\">";
            echo "<div style=\"background:url($board_skin_path/img/level_exp_dot.gif); width:$per%; height:3px;\">&nbsp;</div>";
            echo "</div>";
        }
        ?>
    </td>
    <td width="2" bgcolor="#dedede"><div style="width:2px;"></div></td>
    <td><div style="width:10px;"></div></td>

    <td width="100%" valign="top">
        <table width=100% height="28" cellpadding=0 cellspacing=0 style="background:url(<?=$board_skin_path?>/img/co_title_bg.gif);">
        <tr>
            <!-- 이름, 아이피 -->
            <td>
                <? if ($list[$i][wr_is_mobile]) echo "<img src='$board_skin_path/img/icon_mobile.png' align='absmiddle' class='comment_mobile_icon'>"; ?>
                <? if ($is_admin) { ?> <input type="checkbox" name="chk_comment_id[]" class="chk_comment_id" value="<?=$list[$i][wr_id]?>"> <? } ?>
                <? if ($mw_basic[cf_attribute] == 'qna' && $write[wr_qna_id] == $list[$i][wr_id]) { ?> <img src="<?=$board_skin_path?>/img/icon_choose.png" align="absmiddle"> <? } ?>
                <span class=mw_basic_comment_name><?=$list[$i][name]?></span>
                <? if ($is_ip_view && $list[$i][ip]) { ?> <span class=mw_basic_comment_ip>(<?=$list[$i][ip]?>)</span> <?}?>
                <? if ($history_href) { echo "<a href=\"$history_href\"><img src=\"$board_skin_path/img/btn_comment_history.gif\" align=absmiddle title=\"변경기록\"></a>"; } ?>
                <? if ($list[$i][is_edit]) { echo "<a href=\"javascript:comment_box('{$comment_id}', 'cu');\"><img src='$board_skin_path/img/btn_comment_update.gif' border=0 align=absmiddle title='수정'></a> "; } ?>
                <? if ($list[$i][is_del])  { echo "<a href=\"javascript:comment_delete('{$list[$i][del_link]}');\"><img src='$board_skin_path/img/btn_comment_delete.gif' border=0 align=absmiddle title='삭제'></a> "; } ?>
                <? if ($list[$i][singo_href]) { ?><a href="<?=$list[$i][singo_href]?>"><img src="<?=$board_skin_path?>/img/btn_singo.gif" align=absmiddle title='신고'></a><?}?>
		<? if ($is_admin) { ?>
		<img src="<?=$board_skin_path?>/img/btn_intercept_small.gif" align=absmiddle title='접근차단' style="cursor:pointer" onclick="btn_intercept('<?=$list[$i][mb_id]?>')">
		<img src="<?=$board_skin_path?>/img/btn_ip.gif" align=absmiddle title='IP조회' style="cursor:pointer" onclick="btn_ip('<?=$list[$i][wr_ip]?>')">
		<img src="<?=$board_skin_path?>/img/btn_ip_search.gif" align=absmiddle title='IP검색' style="cursor:pointer" onclick="btn_ip_search('<?=$list[$i][wr_ip]?>')">
		<? } ?>
                <span class=mw_basic_comment_datetime><?=substr($list[$i][wr_datetime],0,10)." (".get_yoil($list[$i][wr_datetime]).") ".substr($list[$i][wr_datetime],11,5)?></span>
            </td>
        </tr>
        </table>

        <table width=100% cellpadding=0 cellspacing=0 class=mw_basic_comment_content>
        <tr>
            <td valign="top">
                <?  if (in_array($list[$i][wr_id], $is_comment_best)) { ?>
                <div id="info_best_reply">
                    베플로 선택된 게시물입니다.
                    <input type="button" value="원문확인▼" id="btn_best_reply_view_<?=$list[$i][wr_id]?>">
                </div>
                <style type="text/css">
                #info_best_reply { margin:0 0 20px 0; color:#F56C07; }
                #view_<?=$list[$i][wr_id]?> { display:none; } 
                #btn_best_reply_view_<?=$list[$i][wr_id]?> { background-color:#fff; color:#444; cursor:pointer; font-size:12px; border:0; }
                </style>
                <script type="text/javascript">
                $("#btn_best_reply_view_<?=$list[$i][wr_id]?>").click(function () {
                    $("#view_<?=$list[$i][wr_id]?>").toggle('slow');
                    if ($("#btn_best_reply_view").val() == "원문확인▲")
                        $("#btn_best_reply_view").val("원문확인▼");
                    else
                        $("#btn_best_reply_view").val("원문확인▲");
                });
                </script>
                <? } ?>
                <!-- 코멘트 출력 -->
                <div id=view_<?=$list[$i][wr_id]?>>
                <?
                $str = $list[$i][content];
                if (strstr($list[$i][wr_option], "secret")) {
                    $str = "<span class='mw_basic_comment_secret'>* $str</span>";
                }
                $str = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $str);
                // FLASH XSS 공격에 의해 주석 처리
                //$str = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(swf)\".*\<\/a\>\]/i", "<script>doc_write(flash_movie('$1://$2.$3'));</script>", $str);
                // 검색시 적용안되는 문제
                //$str = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\"\s*[^\>]*\>[^\s]*\<\/a\>\]/i", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $str);
                $str = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\"\s*[^\>]*\>.*\<\/a\>\]/iUs", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $str);

                $str = preg_replace_callback("/\[code\](.*)\[\/code\]/iU", "_preg_callback", $str);
                echo $str;
                ?>
                </div>
                <? if ($list[$i][trackback]) { echo "<p>".$list[$i][trackback]."</p>"; } ?>
                <? if ($mw_basic[cf_source_copy]) { // 출처 자동 복사 ?>
                <? $copy_url = set_http("{$g4[url]}/{$g4[bbs]}/board.php?bo_table={$bo_table}&wr_id={$wr_id}#c_{$list[$i][wr_id]}"); ?>
                <script type="text/javascript">
                AutoSourcing.setString(<?=$list[$i][wr_id]?> ,"<?=$config[cf_title]?>", "<?=$list[$i][wr_name]?>", "<?=$copy_url?>");
                </script>
                <? } ?>
            </td>
        </tr>
        </table>
        <div style="text-align:right; padding-right:10px;">
            <span class="mw_basic_comment_url" value="<?=$list[$i][wr_id]?>">댓글주소</span>

            <? if ($mw_basic[cf_attribute] == 'qna'
                && !$write[wr_qna_status] && $member[mb_id] && ($member[mb_id] == $write[mb_id] || $is_admin) && !$view[is_notice]) { ?>
                <span class="mw_basic_qna_choose"><a onclick="mw_qna_choose(<?=$list[$i][wr_id]?>)">답변채택</a> <? } ?>
            <? if ($mw_basic[cf_comment_good]) { ?>
                <span class="mw_basic_comment_good"><a onclick="mw_comment_good(<?=$list[$i][wr_id]?>, 'good')">추천</a>
                <span id="mw_comment_good_<?=$list[$i][wr_id]?>"><?=$list[$i][wr_good]?></span></span><? } ?>
            <? if ($mw_basic[cf_comment_nogood]) { ?>
                <span class="mw_basic_comment_nogood"><a onclick="mw_comment_good(<?=$list[$i][wr_id]?>, 'nogood')">반대</a>
                <span id="mw_comment_nogood_<?=$list[$i][wr_id]?>"><?=$list[$i][wr_nogood]?></span></span><? } ?>
            <? if ($list[$i][is_reply]) { echo "<span class='mw_basic_comment_reply'><a href=\"javascript:comment_box('{$comment_id}', 'c');\">답글쓰기</a></span>"; } ?>
        </div>

        <div id='edit_<?=$comment_id?>' style='display:none;'></div><!-- 수정 -->
        <div id='reply_<?=$comment_id?>' style='display:none;'></div><!-- 답변 -->

        <textarea id='save_comment_<?=$comment_id?>' style='display:none;'><?=get_text($list[$i][content1], 0)?></textarea></td>
</tr>
<tr>
    <td colspan="4" height="10"></td>
</tr>
</table>
<? } ?>
</div>
<!-- 코멘트 리스트 -->

<? if ($mw_basic[cf_kcb_comment] && !is_okname()) { ?>
<div style="text-align:center; padding:20px 0 20px 0; margin:10px 0 10px 0; border:1px solid #eaeaea; color:#777;">
    <?=$mw_basic[cf_kcb_type]=='okname'?'실명인증':'성인인증'?> 후 댓글을 입력하실 수 있습니다.
    <a style="cursor:pointer; color:#777;" onclick="win_open('<?=$board_skin_path?>/mw.okname/?bo_table=<?=$bo_table?>', 'okname', 'width=600,height=500')">[인증하기]</a>
</div>
<? } ?>

<? if ($mw_basic[cf_comment_page]) { // 코멘트 페이지 ?>
<div class="mw_basic_comment_page">
<?
/*
$comment_pages = str_replace("처음", "<img src='$board_skin_path/img/page_begin.gif' border='0' align='absmiddle' title='처음'>", $comment_pages);
$comment_pages = str_replace("이전", "<img src='$board_skin_path/img/page_prev.gif' border='0' align='absmiddle' title='이전'>", $comment_pages);
$comment_pages = str_replace("다음", "<img src='$board_skin_path/img/page_next.gif' border='0' align='absmiddle' title='다음'>", $comment_pages);
$comment_pages = str_replace("맨끝", "<img src='$board_skin_path/img/page_end.gif' border='0' align='absmiddle' title='맨끝'>", $comment_pages);
*/
echo $comment_pages;
?>
</div>
<? } ?>

<? if ($is_comment_write || $write_error) { ?>

<!-- 질문 보류 -->
<?
if ($mw_basic[cf_attribute] == 'qna' && ($member[mb_id] == $write[mb_id] || $is_admin) && $write[mb_id] && $write[wr_qna_status] == 0 && !$view[is_notice]) {
    $hold_point = round($write[wr_qna_point] * $mw_basic[cf_qna_hold]/100, 0);
?>
<div class="mw_basic_qna_info">
    <div>
        <b><?=$write[wr_name]?></b>님! 원하시는 답변이 없으면 질문을 보류상태로 변경하실 수 있습니다.
        <a href="javascript:mw_qna_choose(0)">[보류하기]</a>
    </div>
    <? if ($mw_basic[cf_qna_hold]) { ?>
    <div class="info2">
        질문을 보류하면 질문 포인트의 <span class="num"><?=$mw_basic[cf_qna_hold]?>% (<b><?=$hold_point?></b> 포인트)</span> 만 되돌려드립니다.
    </div>
    <? } ?>
</div>
<? } ?>

<!-- 코멘트 입력 -->

<?
// 에디터
if (($mw_basic[cf_comment_editor] && $is_comment_write) || ($mw_basic[cf_admin_dhtml_comment] && $is_admin))
    $is_comment_editor = true;
else
    $is_comment_editor = false;

if ($mw_basic[cf_comment_default] && $is_comment_editor)
    $mw_basic[cf_comment_default] = nl2br($mw_basic[cf_comment_default]);

if (!$mw_basic[cf_editor])
    $mw_basic[cf_editor] = "cheditor";

if ($is_comment_editor && $mw_basic[cf_editor] == "cheditor") {
    /*$g4[cheditor4_path] = "$board_skin_path/cheditor";
    include_once("$board_skin_path/mw.lib/mw.cheditor.lib.php");
    echo "<script type='text/javascript' src='$board_skin_path/cheditor/cheditor.js'></script>";
    echo cheditor1('wr_content', '100%', '100');*/
    include_once("$g4[path]/lib/cheditor4.lib.php");
    echo "<script src='$g4[cheditor4_path]/cheditor.js'></script>";
    echo cheditor1('wr_content', '100%', '100');
}
?>

<div style="padding:5px 0 0 0;">
<a href="javascript:comment_box('', 'c');"><img src="<?=$board_skin_path?>/img/btn_comment_insert.gif" border=0></a>
<? if ($is_admin) { ?><img src="<?=$board_skin_path?>/img/btn_comment_all_delete.gif" border=0 onclick="comment_all_delete()" style="cursor:pointer;"><? } ?>
</div>

<div id=mw_basic_comment_write>

<div id=mw_basic_comment_write_form>

<form name="fviewcomment" method="post" action="./write_comment_update.php" onsubmit="return fviewcomment_submit(this);" autocomplete="off" style="margin:0;" enctype="multipart/form-data">
<input type=hidden name=w           id=w value='c'>
<input type=hidden name=bo_table    value='<?=$bo_table?>'>
<input type=hidden name=wr_id       value='<?=$wr_id?>'>
<input type=hidden name=comment_id  id='comment_id' value=''>
<input type=hidden name=sca         value='<?=$sca?>' >
<input type=hidden name=sfl         value='<?=$sfl?>' >
<input type=hidden name=stx         value='<?=$stx?>'>
<input type=hidden name=spt         value='<?=$spt?>'>
<input type=hidden name=page        value='<?=$page?>'>
<input type=hidden name=cwin        value='<?=$cwin?>'>
<? if ($is_comment_editor) { ?>
<input type=hidden name=html        value='html1'>
<? } ?>

<? if ($is_guest && !$write_error) { ?>
<div style="padding:0 0 2px 0;">
    이름 <input type=text maxlength=20 size=10 name="wr_name" itemname="이름" required class=mw_basic_text <?=$write_error?>>
    패스워드 <input type=password maxlength=20 size=10 name="wr_password" itemname="패스워드" required class=mw_basic_text <?=$write_error?>>
</div>
<?}?>

<div style="padding:2px 0 2px 0;">
    <? if (!$is_comment_editor) { ?>
    <span style="cursor: pointer;" onclick="textarea_decrease('wr_content', 10);"><img src="<?=$board_skin_path?>/img/btn_up.gif" align=absmiddle></span>
    <span style="cursor: pointer;" onclick="textarea_original('wr_content', 5);"><img src="<?=$board_skin_path?>/img/btn_init.gif" align=absmiddle></span>
    <span style="cursor: pointer;" onclick="textarea_increase('wr_content', 10);"><img src="<?=$board_skin_path?>/img/btn_down.gif" align=absmiddle></span>
    <? if ($mw_basic[cf_comment_html]) echo "<input type=\"checkbox\" name=\"html\" value=\"html2\"> html"; ?>
    <? } ?>

    <? if (!$is_comment_editor && ($comment_min || $comment_max)) { ?>
    <?
    if ($comment_min > 0) { echo "$comment_min 글자 이상 "; }
    if ($comment_max > 0) { echo "$comment_max 글자 까지 "; }
    echo " 작성하실수 있습니다. ";
    echo "(현재 <span id=char_count>0</span> 글자 작성하셨습니다.) ";
    ?>
    <?}?>
</div>

<table width=98% cellpadding=0 cellspacing=0 border=0>
<tr>
    <td>
        <? if (!$is_comment_editor || $mw_basic[cf_editor] != "cheditor") { ?>
        <textarea id="wr_content" name="wr_content" rows="6" itemname="내용" required
            <? if (!$write_error) { ?>
                <? if ($is_comment_editor && $mw_basic[cf_editor] == "geditor") echo "geditor gtag=off "; //mode=off"; ?>
            <? } else echo $write_error?>
            <? if (!$is_comment_editor && ($comment_min || $comment_max)) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?}?> class=mw_basic_textarea style='width:98%; word-break:break-all;' ><?=$mw_basic[cf_comment_default]?></textarea>
            <? if (!$is_comment_editor && ($comment_min || $comment_max)) { ?><script type="text/javascript"> check_byte('wr_content', 'char_count'); </script><?}?>
        <? } ?>
        <? if ($is_comment_editor && $mw_basic[cf_editor] == "cheditor") echo "<textarea name='wr_content' id='tx_wr_content'>$mw_basic[cf_comment_default]</textarea>\n" ?>
    </td>
    <td width=60 align=center id="btn_submit">
        <div><input type="image" id="btn_comment_submit" src="<?=$board_skin_path?>/img/btn_comment_ok.gif" border=0 accesskey='s' <?=$write_error?>></div>
        <? if ($good_href || $nogood_href) { // 추천, 비추천?>
        <div style="margin-top:5px;"><img src="<?=$board_skin_path?>/img/btn_comment_good_ok.gif" border=0 onclick="mw_good_act('good')" style="cursor:pointer"></div>
        <div style="margin-top:5px;"><img src="<?=$board_skin_path?>/img/btn_comment_n_good_ok.gif" border=0 onclick="good_submit(fviewcomment, 'good')" style="cursor:pointer"></div>
        <? } ?>
    </td>
</tr>
</table>

<div style="padding:2px 0 2px 0;">
    <? if (!$write_error && !$mw_basic[cf_comment_secret_no]) { ?>
    <input type=checkbox id="wr_secret" name="wr_secret" value="secret" <? if ($mw_basic[cf_comment_secret]) echo "checked" ?>>비밀글 (체크하면 글쓴이만 내용을 확인할 수 있습니다.)
    <? } ?>
    <? if ($mw_basic[cf_anonymous]) {?> <input type="checkbox" name="wr_anonymous" value="1"> 익명 <? } ?>
    <? if ($mw_basic[cf_comment_emoticon] && !$is_comment_editor && !$write_error) {?>
    <span class=mw_basic_comment_emoticon><a href="javascript:win_open('<?=$board_skin_path?>/mw.proc/mw.emoticon.skin.php?bo_table=<?=$bo_table?>','emo','width=600,height=400,scrollbars=yes')">☞ 이모티콘</a></span>
    <? } ?>
    <? if ($mw_basic[cf_comment_file] && !$write_error) { ?>
    <span class=mw_basic_comment_file onclick="$('#comment_file_layer').toggle('slow');">☞ 첨부파일</span>
    <? } ?>
</div>

<? if ($mw_basic[cf_comment_file]) { ?>
<div id="comment_file_layer" style="padding:5px 0 5px 5px; display:none;">
    <input type="file" name="bf_file" size="50" title='파일 용량 <?=$upload_max_filesize?> 이하만 업로드 가능' class="mw_basic_text">
    <input type="checkbox" name="bf_file_del" value="1"> 첨부파일 삭제
</div>
<? } ?>

<? if (file_exists("$g4[bbs_path]/kcaptcha_session.php") && $is_guest && !$write_error) { ?>
<script type="text/javascript"> var md5_norobot_key = ''; </script>
<table border=0 cellpadding=0 cellspacing=0 style="padding:2px 0 2px 0;">
<tr>
    <td width=85>
	<img id='kcaptcha_image'/>
    </td>
    <td>
	<input title="왼쪽의 글자를 입력하세요." type="input" name="wr_key" size="10" itemname="자동등록방지" required class=ed>
	왼쪽의 글자를 입력하세요.
    </td>
</tr>
</table>
<? } elseif ($is_norobot) { ?>
<table border=0 cellpadding=0 cellspacing=0 style="padding:2px 0 2px 0;">
<tr>
    <td width=85>
        <?
        // 이미지 생성이 가능한 경우 자동등록체크코드를 이미지로 만든다.
        if (function_exists("imagecreate") && $mw_basic[cf_norobot_image]) {
            echo "<img src=\"$g4[bbs_path]/norobot_image.php?{$g4['server_time']}\" border=0 align=absmiddle>";
            $norobot_msg = "* 왼쪽의 자동등록방지 코드를 입력하세요.";
        }
        else {
            echo $norobot_str;
            $norobot_msg = "* 왼쪽의 글자중 <FONT COLOR='red'>빨간글자</font>만 순서대로 입력하세요.";
        }
        ?>
    </td>
    <td>
        <input title="왼쪽의 글자중 빨간글자만 순서대로 입력하세요." type=text size=10 name=wr_key itemname="자동등록방지" required class=mw_basic_text <?=$write_error?>>
        <?=$norobot_msg?>
    </td>
</tr>
</table>
<?}?>

</form>

</div>
</div> <!-- 코멘트 입력 끝 -->

<script type="text/javascript" src="<?="$g4[path]/js/jquery.kcaptcha.js"?>"></script>

<script type="text/javascript">
var save_before = '';
var save_html = document.getElementById('mw_basic_comment_write').innerHTML;
function good_submit(f, good) {
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
    if (!document.getElementById('wr_content').value) {
        alert("내용을 입력해주세요.");
        return false;
    }
    if (!fviewcomment_submit(f)) return false;
    $.get("<?=$board_skin_path?>/mw.proc/mw.good.act.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&good="+good, function (data) {
        //alert(data);
        f.submit();
    });
}

function fviewcomment_submit(f)
{
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

    /*
    var s;
    if (s = word_filter_check(document.getElementById('wr_content').value))
    {
        alert("내용에 금지단어('"+s+"')가 포함되어있습니다");
        //document.getElementById('wr_content').focus();
        return false;
    }
    */

    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") echo cheditor3('wr_content'); ?>

    if (document.getElementById('tx_wr_content')) {
        if (!ed_wr_content.outputBodyHTML()) { 
            alert('내용을 입력하십시오.'); 
            ed_wr_content.returnFalse();
            return false;
        }
    }
 
    var subject = "";
    var content = "";
    $.ajax({
        url: "<?=$board_skin_path?>/ajax.filter.php",
        type: "POST",
        data: {
            "subject": "",
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

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        f.wr_content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
    <? if (!$is_comment_editor && ($comment_min || $comment_max)) { ?>
    if (char_min > 0 || char_max > 0)
    {
        check_byte('wr_content', 'char_count');
        var cnt = parseInt(document.getElementById('char_count').innerHTML);
        if (char_min > 0 && char_min > cnt)
        {
            alert("코멘트는 "+char_min+"글자 이상 쓰셔야 합니다.");
            return false;
        } else if (char_max > 0 && char_max < cnt)
        {
            alert("코멘트는 "+char_max+"글자 이하로 쓰셔야 합니다.");
            return false;
        }
    }
    else <? } ?> if (!document.getElementById('wr_content').value)
    {
        alert("코멘트를 입력하여 주십시오.");
        return false;
    }

    if (typeof(f.wr_name) != 'undefined')
    {
        f.wr_name.value = f.wr_name.value.replace(pattern, "");
        if (f.wr_name.value == '')
        {
            alert('이름이 입력되지 않았습니다.');
            f.wr_name.focus();
            return false;
        }
    }

    if (typeof(f.wr_password) != 'undefined')
    {
        f.wr_password.value = f.wr_password.value.replace(pattern, "");
        if (f.wr_password.value == '')
        {
            alert('패스워드가 입력되지 않았습니다.');
            f.wr_password.focus();
            return false;
        }
    }

    if (!check_kcaptcha(f.wr_key)) {
        return false;
    }

    var geditor_status = document.getElementById("geditor_wr_content_geditor_status");
    if (geditor_status != null) {
        if (geditor_status.value == "TEXT") {
            f.html.value = "html2";
        }
        else if (geditor_status.value == "WYSIWYG") {
            f.html.value = "html1";
        }
    }

    $("#btn_submit").html("<img src='<?=$board_skin_path?>/img/icon_loading.gif'>");
    return true;
}

function comment_box(comment_id, work)
{
    var el_id;
    // 코멘트 아이디가 넘어오면 답변, 수정
    if (comment_id)
    {
        if (work == 'c')
            el_id = 'reply_' + comment_id;
        else
            el_id = 'edit_' + comment_id;
    }
    else
        el_id = 'mw_basic_comment_write';

    if (save_before != el_id)
    {
        if (save_before)
        {
            $("#"+save_before).css("display", "none");
            $("#"+save_before).html('');
        }

        $("#"+el_id).css("display", "");
        $("#"+el_id).html(save_html);

        // 코멘트 수정
        if (work == 'cu')
        {
            <? if ($is_comment_editor && $mw_basic[cf_editor] == "cheditor") { ?>
                $("#tx_wr_content").val($("#save_comment_" + comment_id).val());
            <? } else { ?>
                $("#wr_content").val($("#save_comment_" + comment_id).val());

                <? if (!$mw_basic[cf_comment_editor] && ($comment_min || $comment_max)) { ?>
                if (typeof char_count != 'undefined')
                    check_byte('wr_content', 'char_count');
                <? } ?>

            <? } ?>
        }

        $("#comment_id").val(comment_id);
        $("#w").val(work);

        save_before = el_id;

        <? if ($is_comment_editor && $mw_basic[cf_editor] == "cheditor") { ?> ed_wr_content.run(); <? } ?> 
    }

    if (typeof geditor_textareas != "undefined") {
        geditor_load();
    }

    if (work == 'c') {
	<? /*if (file_exists("$g4[bbs_path]/kcaptcha_session.php") && $is_guest && !$write_error) { ?> imageClick();<? }*/ ?>
	<? if (file_exists("$g4[bbs_path]/kcaptcha_session.php") && $is_guest && !$write_error) { ?> $.kcaptcha_run(); <? } ?>
    }


}

<? if ($is_admin) { ?>
function comment_all_delete()
{
    if (!$("input[name=chk_comment_id[]]:checked").length) {
        alert("삭제할 코멘트를 하나 이상 선택하세요.");
        return false;
    }

    if (!confirm("선택한 코멘트를 정말 삭제 하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다")) {
        return false;
    }

    comment_id = $("input[name=chk_comment_id[]]:checked").map(function () { return $(this).val() }).get().join(',');

    $.post("<?=$board_skin_path?>/mw.proc/mw.comment.delete.php", {
        'bo_table' : '<?=$bo_table?>',
        'comment_id' : comment_id,
        'token' : '<?=$token?>' },
    function (ret) {
        if (ret == 'ok')
            location.reload();
        else
            alert(ret);
    });
}
<? } ?>

//$(document).ready(function () {
    comment_box('', 'c');
//});
</script>

<? } ?>

<script type="text/javascript">
function comment_delete(url)
{
    if (confirm("이 코멘트를 삭제하시겠습니까?")) location.href = url;
}
</script>

<? if ($mw_basic[cf_attribute] == 'qna' && !$write[wr_qna_status] && $member[mb_id] && ($member[mb_id] == $write[mb_id] || $is_admin) && !$view[is_notice]) { ?>
<script type="text/javascript">
function mw_qna_choose(wr_id) {
    if (wr_id) {
        if (!confirm("이 답변을 채택하시겠습니까?")) return;
    } else {
        if (!confirm("이 질문을 보류하시겠습니까?")) return;
    }

    $.get("<?=$board_skin_path?>/mw.proc/mw.qna.choose.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&choose_id="+wr_id, function (data) {
        data = data.split('|');
        alert(data[0]);
        if (data[1] == 'ok') location.reload();
    });
}
</script>
<? } ?>

<?  if ($mw_basic[cf_comment_good] || $mw_basic[cf_comment_nogood]) { ?>
<script type="text/javascript">
function mw_comment_good(wr_id, good) {
    $.get("<?=$board_skin_path?>/mw.proc/mw.comment.good.php?bo_table=<?=$bo_table?>&parent_id=<?=$wr_id?>&wr_id="+wr_id+"&good="+good, function (data) {
        data = data.split('|');
        alert(data[0]);
        if (good == 'good') {
            $("#mw_comment_good_"+wr_id).html(data[1]);
        } else {
            $("#mw_comment_nogood_"+wr_id).html(data[1]);
        }
    });
}
</script>
<? } ?>

<? if ($is_comment_editor && $mw_basic[cf_editor] == "geditor") { ?>
<script type="text/javascript">
var g4_skin_path = "<?=$board_skin_path?>";
</script>
<script type="text/javascript" src="<?=$board_skin_path?>/mw.geditor/geditor.js"></script>
<? } ?>

<? if ($mw_basic[cf_icon_level]) { ?>
<style type="text/css">
<? for ($i=0; $i<=99; $i++) { ?>
#mw_basic .icon_level<?=$i?> { background:url(<?=$board_skin_path?>/img/icon_level.png) 0 -<?=($i*10)?>px; width:50px; height:10px; font-size:10px; line-height:10px; }
<? } ?>
</style>
<? } ?>


<? if($cwin==1) { ?>
</td><tr></table><p align=center><a href="javascript:window.close();"><img src="<?=$board_skin_path?>/img/btn_close.gif" border="0"></a><br><br></div>
<?}?>

<? } // 실명인증 ?>

<? if ($cwin) { ?> <script type="text/javascript" src="<?=$board_skin_path?>/mw.js/ZeroClipboard.js"></script> <? } ?>
<script type="text/javascript">
$(document).ready(function () {
    $(".mw_basic_comment_url").click(function () {
        var comment_id = $(this).attr("value");
        var top = $(this).offset().top + 15 ;
        var left = $(this).offset().left;

        if ($("#comment_url_popup").css("display") != "block" || comment_id != old_comment_id) {
            $(this).append("<img src='<?=$board_skin_path?>/img/icon_loading.gif' style='position:absolute;' id='comment_url_loading'>");
            $.get("<?=$board_skin_path?>/mw.proc/mw.get.comment.url.php", {
                "bo_table" : "<?=$bo_table?>",
                "wr_id" : comment_id
            }, function (dat) {
                $("#comment_url").html(dat);
                $("#comment_url_popup").css("display", "block");
                $("#comment_url_popup").css("position", "absolute");
                $("#comment_url_popup").css("top", top);
                $("#comment_url_popup").css("left", left - $("#comment_url_popup").width()+50);
                old_comment_id = comment_id;

                $("#comment_url_loading").remove();

                var clipBoardComment = new ZeroClipboard.Client();
                ZeroClipboard.setMoviePath("<?=$board_skin_path?>/mw.js/ZeroClipboard.swf");
                clipBoardComment.setHandCursor(true);
                clipBoardComment.addEventListener('mouseOver', function (client) {
                    clipBoardComment.setText($("#comment_url_result").text());
                });
                clipBoardComment.addEventListener('complete', function (client) {
                    alert("클립보드에 복사되었습니다. \'Ctrl+V\'를 눌러 붙여넣기 해주세요.");
                    $("#comment_url").html("");
                    $("#comment_url_popup").css("display", "none");
                });  
                clipBoardComment.glue("comment_url_copy");
            });
        } else {
            $("#comment_url").html("");
            $("#comment_url_popup").css("display", "none");
        }
    });
});
</script>
<div id="comment_url_popup" style="display:none;">
<table border="0" cellpadding="0" cellspacing="0" height="53" background="<?=$board_skin_path?>/img/pg.png">
<tr>
    <td width="5"valign="top"><img src="<?=$board_skin_path?>/img/pl.png"></td>
    <td valign="top" align="left">
        <div style="height:13px; margin:0 20px 0 0; text-align:right;"><img src="<?=$board_skin_path?>/img/ps.png" height="13"></div>
        <div id="comment_url"></div>
    </td>
    <td width="5" valign="top"><img src="<?=$board_skin_path?>/img/pr.png"></td>
</tr>
</table>
</div>

<? if ($cwin) { ?>
<script type="text/javascript"> document.title = "<?=mw_reg_str(addslashes($write[wr_subject]))?>"; </script>
<script type="text/javascript">
function btn_ip_search(ip) {
    win_open("<?=$g4[admin_path]?>/member_list.php?sfl=mb_ip&stx=" + ip);
}
</script>
<? if ($mw_basic[cf_singo]) { ?>
<script type="text/javascript">
function btn_singo(wr_id, parent_id) {
    //if (confirm("이 게시물을 정말 신고하시겠습니까?")) {
    //hiddenframe.location.href = "<?=$board_skin_path?>/mw.proc/mw.btn.singo.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id + "&parent_id=" + parent_id;
    win_open("<?=$board_skin_path?>/mw.proc/mw.btn.singo.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id + "&parent_id=" + parent_id, "win_singo", "width=500,height=300,scrollbars=yes");
    //}
}
function btn_singo_view(wr_id) {
    var id = "singo_block_" + wr_id;

    if (document.getElementById(id).style.display == 'block')
        document.getElementById(id).style.display = 'none';
    else
        document.getElementById(id).style.display = 'block';
}

function btn_singo_clear(wr_id) {
    if (confirm("정말 초기화 하시겠습니까?")) {
        $.get("<?=$board_skin_path?>/mw.proc/mw.btn.singo.clear.php?bo_table=<?=$bo_table?>&wr_id="+wr_id, function(msg) {
            alert(msg);
        });
    }
}
</script>
<? } ?>

<? } ?>

<style type="text/css">
/* 댓글 img */
#mw_basic .mw_basic_comment_content img {
    max-width:<?=$board[bo_image_width]?>px;
    height:auto; 
}
</style>

