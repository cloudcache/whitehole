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

if ($is_admin != "super")
    alert_close("접근 권한이 없습니다.");

if (!$mw_basic[cf_thumb_width]) $mw_basic[cf_thumb_width] = 80;
if (!$mw_basic[cf_thumb_height]) $mw_basic[cf_thumb_height] = 50;

if ($cf_age && $cf_age_type)  {
    $cf_age .= $cf_age_type;
} else {
    $cf_age = '';
}

if ($cf_board_sdate) {
    $tmp = explode("-", $cf_board_sdate);
    if (!@checkdate($tmp[1], $tmp[2], $tmp[0]))
        alert("게시판 접근 가능 날짜가 올바르지 않습니다.");
}

if ($cf_board_edate) {
    $tmp = explode("-", $cf_board_edate);
    if (!@checkdate($tmp[1], $tmp[2], $tmp[0]))
        alert("게시판 접근 가능 날짜가 올바르지 않습니다.");
}

if ($cf_board_sdate and $cf_board_edate and $cf_board_sdate > $cf_board_edate) {
    alert("게시판 접근가능 시작날짜와 종료날짜가 바뀐것 같습니다.");
}

$cf_board_stime = $cf_board_etime = "";

if ($cf_board_stime_hour and $cf_board_stime_minute)
    $cf_board_stime = "$cf_board_stime_hour:$cf_board_stime_minute:00";

if ($cf_board_etime_hour and $cf_board_etime_minute)
    $cf_board_etime = "$cf_board_etime_hour:$cf_board_etime_minute:59";

for ($i=0; $i<7; $i++) {
    if ($cf_board_week[$i])
        $cf_board_week[$i] = '1';
    else
        $cf_board_week[$i] = '0';
}
$cf_board_week = implode(",", $cf_board_week);
if ($cf_board_week == '0,0,0,0,0,0,0' || $cf_board_week == '') {
    $cf_board_week = '1,1,1,1,1,1,1';
}

$cf_sns = '';
if ($cf_sns_twitter) $cf_sns.= '/twitter/';
if ($cf_sns_me2day) $cf_sns.= '/me2day/';
if ($cf_sns_yozm) $cf_sns.= '/yozm/';
if ($cf_sns_cyworld) $cf_sns.= '/cyworld/';
if ($cf_sns_facebook) $cf_sns.= '/facebook/';
if ($cf_sns_facebook_good) $cf_sns.= '/facebook_good/';
if ($cf_sns_google_plus) $cf_sns.= '/google_plus/';

$cf_auto_move = array();
$cf_auto_move['use'] = $cf_auto_move_use;
$cf_auto_move['bo_table'] = $cf_auto_move_bo_table;
$cf_auto_move['good'] = $cf_auto_move_good;
$cf_auto_move['nogood'] = $cf_auto_move_nogood;
$cf_auto_move['hit'] = $cf_auto_move_hit;
$cf_auto_move['singo'] = $cf_auto_move_singo;
$cf_auto_move['comment'] = $cf_auto_move_comment;
$cf_auto_move['sub'] = $cf_auto_move_sub;
$cf_auto_move['day'] = $cf_auto_move_day;
$cf_auto_move = serialize($cf_auto_move);

if ($cf_contents_shop) {
    sql_query("alter table $write_table add wr_contents_price int not null", false);
    sql_query("alter table $write_table add wr_contents_domain char(1) not null", false);
    sql_query("alter table $write_table add wr_contents_preview text not null", false);
}

$sql = "update $mw[basic_config_table] set
bo_table = '$bo_table'
,cf_type = '$cf_type'
,cf_thumb_width = '$cf_thumb_width'
,cf_thumb_height = '$cf_thumb_height'
,cf_thumb2_width = '$cf_thumb2_width'
,cf_thumb2_height = '$cf_thumb2_height'
,cf_thumb3_width = '$cf_thumb3_width'
,cf_thumb3_height = '$cf_thumb3_height'
,cf_thumb4_width = '$cf_thumb4_width'
,cf_thumb4_height = '$cf_thumb4_height'
,cf_thumb5_width = '$cf_thumb5_width'
,cf_thumb5_height = '$cf_thumb5_height'
,cf_resize_original = '$cf_resize_original'
,cf_resize_quality = '$cf_resize_quality'
,cf_resize_base = '$cf_resize_base'
,cf_attribute = '$cf_attribute'
,cf_ccl = '$cf_ccl'
,cf_age = '$cf_age'
,cf_gender = '$cf_gender'
,cf_board_sdate = '$cf_board_sdate'
,cf_board_edate = '$cf_board_edate'
,cf_board_stime = '$cf_board_stime'
,cf_board_etime = '$cf_board_etime'
,cf_board_week = '$cf_board_week'
,cf_hot = '$cf_hot'
,cf_hot_basis = '$cf_hot_basis'
,cf_hot_limit = '$cf_hot_limit'
,cf_related = '$cf_related'
,cf_latest = '$cf_latest'
,cf_sns = '$cf_sns'
,cf_link_blank = '$cf_link_blank'
,cf_comma = '$cf_comma'
,cf_search_top = '$cf_search_top'
,cf_category_tab = '$cf_category_tab'
,cf_category_radio = '$cf_category_radio'
,cf_notice_top = '$cf_notice_top'
,cf_notice_top_length = '$cf_notice_top_length'
,cf_zzal = '$cf_zzal'
,cf_zzal_must = '$cf_zzal_must'
,cf_source_copy = '$cf_source_copy'
,cf_relation = '$cf_relation'
,cf_comment_editor = '$cf_comment_editor'
,cf_editor = '$cf_editor'
,cf_comment_file = '$cf_comment_file'
,cf_comment_page = '$cf_comment_page'
,cf_comment_page_rows = '$cf_comment_page_rows'
,cf_comment_html = '$cf_comment_html'
,cf_comment_emoticon = '$cf_comment_emoticon'
,cf_post_emoticon = '$cf_post_emoticon'
,cf_comment_write = '$cf_comment_write'
,cf_singo = '$cf_singo'
,cf_singo_id = '$cf_singo_id'
,cf_memo_id = '$cf_memo_id'
,cf_email = '$cf_email'
,cf_sms_id = '$cf_sms_id'
,cf_sms_pw = '$cf_sms_pw'
,cf_hp = '$cf_hp'
,cf_file_head = '$cf_file_head'
,cf_file_tail = '$cf_file_tail'
,cf_content_head = '$cf_content_head'
,cf_content_tail = '$cf_content_tail'
,cf_comment_notice = '$cf_comment_notice'
,cf_download_comment = '$cf_download_comment'
,cf_download_good = '$cf_download_good'
,cf_download_popup = '$cf_download_popup'
,cf_download_popup_w = '$cf_download_popup_w'
,cf_download_popup_h = '$cf_download_popup_h'
,cf_download_popup_msg = '$cf_download_popup_msg'
,cf_uploader_day = '$cf_uploader_day'
,cf_uploader_point = '$cf_uploader_point'
,cf_norobot_image = '$cf_norobot_image'
,cf_comment_secret = '$cf_comment_secret'
,cf_comment_secret_no = '$cf_comment_secret_no'
,cf_desc_len = '$cf_desc_len'
,cf_write_button = '$cf_write_button'
,cf_subject_link = '$cf_subject_link'
,cf_comment_ban = '$cf_comment_ban'
,cf_comment_ban_level = '$cf_comment_ban_level'
,cf_comment_period = '$cf_comment_period'
,cf_download_log = '$cf_download_log'
,cf_link_log = '$cf_link_log'
,cf_post_history = '$cf_post_history'
,cf_delete_log = '$cf_delete_log'
,cf_post_history_level = '$cf_post_history_level'
,cf_comment_default = '$cf_comment_default'
,cf_link_board = '$cf_link_board'
,cf_link_target_level = '$cf_link_target_level'
,cf_link_write = '$cf_link_write'
,cf_bomb_level = '$cf_bomb_level'
,cf_bomb_days = '$cf_bomb_days'
,cf_move_level = '$cf_move_level'
,cf_download_date = '$cf_download_date'
,cf_auto_move = '$cf_auto_move'
,cf_list_shuffle = '$cf_list_shuffle'
,cf_sns_datetime = '$cf_sns_datetime'
,cf_read_point_message = '$cf_read_point_message'
,cf_insert_subject = '$cf_insert_subject'
,cf_notice_name = '$cf_notice_name'
,cf_notice_date = '$cf_notice_date'
,cf_notice_hit = '$cf_notice_hit'
,cf_post_name = '$cf_post_name'
,cf_post_date = '$cf_post_date'
,cf_post_hit = '$cf_post_hit'
,cf_list_good = '$cf_list_good'
,cf_post_num = '$cf_post_num'
,cf_img_1_noview = '$cf_img_1_noview'
,cf_only_one = '$cf_only_one'
,cf_must_notice = '$cf_must_notice'
,cf_must_notice_read = '$cf_must_notice_read'
,cf_must_notice_comment = '$cf_must_notice_comment'
,cf_must_notice_down = '$cf_must_notice_down'
,cf_comment_good = '$cf_comment_good'
,cf_comment_nogood = '$cf_comment_nogood'
,cf_comment_best = '$cf_comment_best'
,cf_comment_best_limit = '$cf_comment_best_limit'
,cf_icon_level = '$cf_icon_level'
,cf_icon_level_point = '$cf_icon_level_point'
,cf_iframe_level = '$cf_iframe_level'
,cf_good_point = '$cf_good_point'
,cf_good_re_point = '$cf_good_re_point'
,cf_nogood_point = '$cf_nogood_point'
,cf_nogood_re_point = '$cf_nogood_re_point'
,cf_comment_good_point = '$cf_comment_good_point'
,cf_comment_good_re_point = '$cf_comment_good_re_point'
,cf_comment_nogood_point = '$cf_comment_nogood_point'
,cf_good_days = '$cf_good_days'
,cf_good_cancel_days = '$cf_good_cancel_days'
,cf_good_count = '$cf_good_count'
,cf_social_commerce = '$cf_social_commerce'
,cf_google_map = '$cf_google_map'
,cf_comment_nogood_re_point = '$cf_comment_nogood_re_point'
,cf_change_image_size = '$cf_change_image_size'
,cf_change_image_size_level = '$cf_change_image_size_level'
,cf_replace_word = '$cf_replace_word'
,cf_view_good = '$cf_view_good'
,cf_name_title = '$cf_name_title'
,cf_attach_count = '$cf_attach_count'
,cf_related_table = '$cf_related_table'
,cf_latest_table = '$cf_latest_table'
,cf_anonymous = '$cf_anonymous'
,cf_contents_shop = '$cf_contents_shop'
,cf_contents_shop_download_count = '$cf_contents_shop_download_count'
,cf_contents_shop_download_day = '$cf_contents_shop_download_day'
,cf_contents_shop_write = '$cf_contents_shop_write'
,cf_contents_shop_write_cash = '$cf_contents_shop_write_cash'
,cf_contents_shop_uploader = '$cf_contents_shop_uploader'
,cf_contents_shop_uploader_cash = '$cf_contents_shop_uploader_cash'
,cf_contents_shop_fix = '$cf_contents_shop_fix'
,cf_contents_shop_max = '$cf_contents_shop_max'
,cf_contents_shop_min = '$cf_contents_shop_min'
,cf_admin_dhtml = '$cf_admin_dhtml'
,cf_admin_dhtml_comment = '$cf_admin_dhtml_comment'
,cf_write_notice = '$cf_write_notice'
,cf_thumb_keep = '$cf_thumb_keep'
,cf_thumb2_keep = '$cf_thumb2_keep'
,cf_thumb3_keep = '$cf_thumb3_keep'
,cf_thumb4_keep = '$cf_thumb4_keep'
,cf_thumb5_keep = '$cf_thumb5_keep'
,cf_css = '$cf_css'
,cf_exif = '$cf_exif'
,cf_no_img_ext = '$cf_no_img_ext'
,cf_print = '$cf_print'
,cf_umz = '$cf_umz'
,cf_umz2 = '$cf_umz2'
,cf_shorten = '$cf_shorten'
,cf_board_member = '$cf_board_member'
,cf_board_member_list = '$cf_board_member_list'
,cf_board_member_view = '$cf_board_member_view'
,cf_include_view_top = '$cf_include_view_top'
,cf_include_view_head = '$cf_include_view_head'
,cf_include_view = '$cf_include_view'
,cf_include_view_tail = '$cf_include_view_tail'
,cf_include_file_head = '$cf_include_file_head'
,cf_include_file_tail = '$cf_include_file_tail'
,cf_include_head = '$cf_include_head'
,cf_include_tail = '$cf_include_tail'
,cf_include_list_main = '$cf_include_list_main'
,cf_include_comment_main = '$cf_include_comment_main'
,cf_subject_style = '$cf_subject_style'
,cf_subject_style_level = '$cf_subject_style_level'
,cf_subject_style_color_default = '$cf_subject_style_color_default'
,cf_subject_style_color_picker = '$cf_subject_style_color_picker'
,cf_guploader = '$cf_guploader'
,cf_under_construction = '$cf_under_construction'
,cf_no_delete = '$cf_no_delete'
,cf_write_point = '$cf_write_point'
,cf_write_register = '$cf_write_register'
,cf_write_day = '$cf_write_day'
,cf_write_day_count = '$cf_write_day_count'
,cf_comment_point = '$cf_comment_point'
,cf_comment_register = '$cf_comment_register'
,cf_comment_day = '$cf_comment_day'
,cf_comment_day_count = '$cf_comment_day_count'
,cf_comment_write_count = '$cf_comment_write_count'
,cf_read_point = '$cf_read_point'
,cf_read_register = '$cf_read_register'
,cf_vote = '$cf_vote'
,cf_vote_level = '$cf_vote_level'
,cf_vote_join_level = '$cf_vote_join_level'
,cf_quiz = '$cf_quiz'
,cf_quiz_level = '$cf_quiz_level'
,cf_quiz_join_level = '$cf_quiz_join_level'
,cf_collect = '$cf_collect'
,cf_talent_market = '$cf_talent_market'
,cf_talent_market_commission = '$cf_talent_market_commission'
,cf_talent_market_min = '$cf_talent_market_min'
,cf_talent_market_max = '$cf_talent_market_max'
,cf_talent_market_min_point = '$cf_talent_market_min_point'
,cf_talent_market_max_point = '$cf_talent_market_max_point'
,cf_talent_market_app = '$cf_talent_market_app'
,cf_read_level = '$cf_read_level'
,cf_read_level_own = '$cf_read_level_own'
,cf_reward = '$cf_reward'
,cf_good_graph = '$cf_good_graph'
,cf_singo_after = '$cf_singo_after'
,cf_singo_number = '$cf_singo_number'
,cf_singo_id_block = '$cf_singo_id_block'
,cf_singo_write_block = '$cf_singo_write_block'
,cf_singo_write_secret = '$cf_singo_write_secret'
,cf_singo_level = '$cf_singo_level'
,cf_singo_writer = '$cf_singo_writer'
,cf_watermark_use = '$cf_watermark_use'
,cf_watermark_use_thumb = '$cf_watermark_use_thumb'
,cf_watermark_path = '$cf_watermark_path'
,cf_watermark_position = '$cf_watermark_position'
,cf_watermark_transparency = '$cf_watermark_transparency'
,cf_kcb_id = '$cf_kcb_id'
,cf_kcb_list = '$cf_kcb_list'
,cf_kcb_read = '$cf_kcb_read'
,cf_kcb_write = '$cf_kcb_write'
,cf_kcb_comment = '$cf_kcb_comment'
,cf_kcb_type = '$cf_kcb_type'
,cf_kcb_post = '$cf_kcb_post'
,cf_kcb_post_level = '$cf_kcb_post_level'
,cf_qna_point_use = '$cf_qna_point_use'
,cf_qna_point_min = '$cf_qna_point_min'
,cf_qna_point_max = '$cf_qna_point_max'
,cf_qna_point_add = '$cf_qna_point_add'
,cf_qna_save = '$cf_qna_save'
,cf_qna_hold = '$cf_qna_hold'
,cf_qna_count = '$cf_qna_count'
,cf_lucky_writing_ment = '$cf_lucky_writing_ment'
,cf_lucky_writing_comment = '$cf_lucky_writing_comment'
,cf_lucky_writing_chance = '$cf_lucky_writing_chance'
,cf_lucky_writing_point_start = '$cf_lucky_writing_point_start'
,cf_lucky_writing_point_end = '$cf_lucky_writing_point_end'
,cf_lucky_writing_comment_chance = '$cf_lucky_writing_comment_chance'
,cf_lucky_writing_comment_point_start = '$cf_lucky_writing_comment_point_start'
,cf_lucky_writing_comment_point_end = '$cf_lucky_writing_comment_point_end'
where bo_table = '$bo_table'";
sql_query($sql);
//,cf_preview_level = '$cf_preview_level'
//,cf_preview_size = '$cf_preview_size'

//if ($bo_insert_content) {
    sql_query("update $g4[board_table] set bo_insert_content = '$bo_insert_content' where bo_table = '$bo_table'");
//}

if ($cf_lucky_writing_name) {
    $mb = get_member("@lucky-writing", "mb_nick");
    if (!$mb) {
        sql_query(" insert into $g4[member_table] set mb_id = '@lucky-writing', mb_name = '$cf_lucky_writing_name', mb_nick = '럭키라이팅', mb_datetime = '$g4[time_ymdhis]' ");
    } else if ($mb[mb_nick] != $cf_lucky_writing_name) {
        sql_query("update $g4[member_table] set mb_nick = '$cf_lucky_writing_name' where mb_id = '@lucky-writing'");
    }
}

// 배추 베이직 스킨을 사용하는 모든 게시판을 찾아 환경설정 정보를 입력
// (환경설정 정보가 기존에 없던 것들만!)
$sql = "select * from $g4[board_table] where bo_skin = '$board[bo_skin]'";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    $sql = "insert into $mw[basic_config_table] set gr_id = '$row[gr_id]', bo_table = '$row[bo_table]'";
    $qry = sql_query($sql, false);
}

// 전체 적용
$sql = "update $mw[basic_config_table] set bo_table = bo_table ";
$def = "update $mw[basic_config_table] set bo_table = bo_table ";

if ($chk[cf_type]) $sql .= ", cf_type = '$cf_type' ";
if ($chk[cf_thumb]) $sql .= ", cf_thumb_width = '$cf_thumb_width', cf_thumb_height = '$cf_thumb_height', cf_thumb_keep = '$cf_thumb_keep'";
if ($chk[cf_thumb2]) $sql .= ", cf_thumb2_width = '$cf_thumb2_width', cf_thumb2_height = '$cf_thumb2_height', cf_thumb2_keep = '$cf_thumb2_keep' ";
if ($chk[cf_thumb3]) $sql .= ", cf_thumb3_width = '$cf_thumb3_width', cf_thumb3_height = '$cf_thumb3_height', cf_thumb3_keep = '$cf_thumb3_keep' ";
if ($chk[cf_thumb4]) $sql .= ", cf_thumb4_width = '$cf_thumb4_width', cf_thumb4_height = '$cf_thumb4_height', cf_thumb4_keep = '$cf_thumb4_keep' ";
if ($chk[cf_thumb5]) $sql .= ", cf_thumb5_width = '$cf_thumb5_width', cf_thumb5_height = '$cf_thumb5_height', cf_thumb5_keep = '$cf_thumb5_keep' ";
if ($chk[cf_resize_original]) $sql .= ", cf_resize_original = '$cf_resize_original' ";
if ($chk[cf_resize_quality]) $sql .= ", cf_resize_quality = '$cf_resize_quality' ";
if ($chk[cf_resize_base]) $sql .= ", cf_resize_base = '$cf_resize_base' ";
if ($chk[cf_attribute]) $sql .= ", cf_attribute = '$cf_attribute' ";
if ($chk[cf_qna_point_use]) {
    $sql .= ", cf_qna_point_use = '$cf_qna_point_use' ";
    $sql .= ", cf_qna_point_min = '$cf_qna_point_min' ";
    $sql .= ", cf_qna_point_max = '$cf_qna_point_max' ";
    $sql .= ", cf_qna_point_add = '$cf_qna_point_add' ";
    $sql .= ", cf_qna_save = '$cf_qna_save' ";
    $sql .= ", cf_qna_hold = '$cf_qna_hold' ";
    $sql .= ", cf_qna_count = '$cf_qna_count' ";
}
if ($chk[cf_ccl]) $sql .= ", cf_ccl = '$cf_ccl' ";
if ($chk[cf_gender]) $sql .= ", cf_gender = '$cf_gender' ";
if ($chk[cf_board_date]) {
    $sql .= ", cf_board_sdate = '$cf_board_sdate' ";
    $sql .= ", cf_board_edate = '$cf_board_edate' ";
}
if ($chk[cf_board_time]) {
    $sql .= ", cf_board_stime = '$cf_board_stime' ";
    $sql .= ", cf_board_etime = '$cf_board_etime' ";
}
if ($chk[cf_board_week]) $sql .= ", cf_board_week = '$cf_board_week' ";
if ($chk[cf_age]) $sql .= ", cf_age = '$cf_age' ";
if ($chk[cf_hot]) $sql .= ", cf_hot = '$cf_hot', cf_hot_basis = '$cf_hot_basis', cf_hot_limit = '$cf_hot_limit'  ";
if ($chk[cf_related]) $sql .= ", cf_related = '$cf_related' ";
if ($chk[cf_latest]) $sql .= ", cf_latest = '$cf_latest' ";
if ($chk[cf_sns]) $sql .= ", cf_sns = '$cf_sns' ";
if ($chk[cf_zzal]) $sql .= ", cf_zzal = '$cf_zzal', cf_zzal_must = '$cf_zzal_must' ";
if ($chk[cf_link_blank]) $sql .= ", cf_link_blank = '$cf_link_blank' ";
if ($chk[cf_comma]) $sql .= ", cf_comma = '$cf_comma' ";
if ($chk[cf_search_top]) $sql .= ", cf_search_top = '$cf_search_top' ";
if ($chk[cf_category_tab]) $sql .= ", cf_category_tab = '$cf_category_tab' ";
if ($chk[cf_category_radio]) $sql .= ", cf_category_radio = '$cf_category_radio' ";
if ($chk[cf_notice_top]) $sql .= ", cf_notice_top = '$cf_notice_top' ";
if ($chk[cf_notice_top_length]) $sql .= ", cf_notice_top_length = '$cf_notice_top_length' ";
if ($chk[cf_source_copy]) $sql .= ", cf_source_copy = '$cf_source_copy' ";
if ($chk[cf_relation]) $sql .= ", cf_relation = '$cf_relation' ";
if ($chk[cf_comment_editor]) $sql .= ", cf_comment_editor = '$cf_comment_editor' ";
if ($chk[cf_editor]) $sql .= ", cf_editor = '$cf_editor' ";
if ($chk[cf_comment_file]) $sql .= ", cf_comment_file = '$cf_comment_file' ";
if ($chk[cf_comment_page]) {
    $sql .= ", cf_comment_page = '$cf_comment_page' ";
    $sql .= ", cf_comment_page_rows = '$cf_comment_page_rows' ";
}
if ($chk[cf_comment_emoticon]) $sql .= ", cf_comment_emoticon = '$cf_comment_emoticon' ";
if ($chk[cf_post_emoticon]) $sql .= ", cf_post_emoticon = '$cf_post_emoticon' ";
if ($chk[cf_comment_write]) $sql .= ", cf_comment_write = '$cf_comment_write' ";
if ($chk[cf_comment_html]) $sql .= ", cf_comment_html = '$cf_comment_html' ";
if ($chk[cf_singo]) $sql .= ", cf_singo = '$cf_singo' ";
if ($chk[cf_singo_id]) $sql .= ", cf_singo_id = '$cf_singo_id' ";
if ($chk[cf_memo_id]) $sql .= ", cf_memo_id = '$cf_memo_id' ";
if ($chk[cf_email]) $sql .= ", cf_email = '$cf_email' ";
if ($chk[cf_hp]) $sql .= ", cf_hp = '$cf_hp', cf_sms_id = '$cf_sms_id', cf_sms_pw = '$cf_sms_pw' ";
if ($chk[cf_file_head]) $sql .= ", cf_file_head = '$cf_file_head' ";
if ($chk[cf_file_tail]) $sql .= ", cf_file_tail = '$cf_file_tail' ";
if ($chk[cf_content_head]) $sql .= ", cf_content_head = '$cf_content_head' ";
if ($chk[cf_content_tail]) $sql .= ", cf_content_tail = '$cf_content_tail' ";
if ($chk[cf_comment_notice]) $sql .= ", cf_comment_notice = '$cf_comment_notice' ";
if ($chk[cf_download_comment]) {
    $sql .= ", cf_download_comment = '$cf_download_comment' ";
    $sql .= ", cf_download_good = '$cf_download_good' ";
}
if ($chk[cf_download_popup]) $sql .= ", cf_download_popup = '$cf_download_popup' ";
if ($chk[cf_download_popup_size]) {
    $sql .= ", cf_download_popup_w = '$cf_download_popup_w' ";
    $sql .= ", cf_download_popup_h = '$cf_download_popup_h' ";
}
if ($chk[cf_download_popup_msg]) $sql .= ", cf_download_popup_msg = '$cf_download_popup_msg' ";
if ($chk[cf_uploader_point]) $sql .= ", cf_uploader_day = '$cf_uploader_day', cf_uploader_point = '$cf_uploader_point' ";
if ($chk[cf_norobot_image]) $sql .= ", cf_norobot_image = '$cf_norobot_image' ";
if ($chk[cf_desc_len]) $sql .= ", cf_desc_len = '$cf_desc_len' ";
if ($chk[cf_write_button]) $sql .= ", cf_write_button = '$cf_write_button' ";
if ($chk[cf_subject_link]) $sql .= ", cf_subject_link = '$cf_subject_link' ";
if ($chk[cf_comment_ban]) $sql .= ", cf_comment_ban = '$cf_comment_ban' ";
if ($chk[cf_comment_ban_level]) $sql .= ", cf_comment_ban_level = '$cf_comment_ban_level' ";
if ($chk[cf_comment_period]) $sql .= ", cf_comment_period = '$cf_comment_period' ";
if ($chk[cf_download_log]) $sql .= ", cf_download_log = '$cf_download_log' ";
if ($chk[cf_link_log]) $sql .= ", cf_link_log = '$cf_link_log' ";
if ($chk[cf_post_history]) $sql .= ", cf_post_history = '$cf_post_history' ";
if ($chk[cf_delete_log]) $sql .= ", cf_delete_log = '$cf_delete_log' ";
if ($chk[cf_post_history_level]) $sql .= ", cf_post_history_level = '$cf_post_history_level' ";
if ($chk[cf_link_board]) $sql .= ", cf_link_board = '$cf_link_board' ";
if ($chk[cf_link_target_level]) $sql .= ", cf_link_target_level = '$cf_link_target_level' ";
if ($chk[cf_link_write]) $sql .= ", cf_link_write = '$cf_link_write' ";
if ($chk[cf_bomb_level]) {
    $sql .= ", cf_bomb_level = '$cf_bomb_level' ";
    $sql .= ", cf_bomb_days = '$cf_bomb_days' ";
}
if ($chk[cf_move_level]) $sql .= ", cf_move_level = '$cf_move_level' ";
if ($chk[cf_download_date]) $sql .= ", cf_download_date = '$cf_download_date' ";
if ($chk[cf_auto_move]) $sql .= ", cf_auto_move = '$cf_auto_move' ";
if ($chk[cf_comment_default]) $sql .= ", cf_comment_default = '$cf_comment_default' ";
if ($chk[cf_list_shuffle]) $sql .= ", cf_list_shuffle = '$cf_list_shuffle' ";
if ($chk[cf_sns_datetime]) $sql .= ", cf_sns_datetime = '$cf_sns_datetime' ";
if ($chk[cf_read_point_message]) $sql .= ", cf_read_point_message = '$cf_read_point_message' ";
if ($chk[cf_insert_subject]) $sql .= ", cf_insert_subject = '$cf_insert_subject' ";
if ($chk[bo_insert_content]) {
    sql_query("update $g4[board_table] set bo_insert_content = '$bo_insert_content' where gr_id = '$gr_id'");
}
if ($chk[cf_notice_name]) $sql .= ", cf_notice_name = '$cf_notice_name' ";
if ($chk[cf_notice_date]) $sql .= ", cf_notice_date = '$cf_notice_date' ";
if ($chk[cf_notice_hit]) $sql .= ", cf_notice_hit = '$cf_notice_hit' ";
if ($chk[cf_post_name]) $sql .= ", cf_post_name = '$cf_post_name' ";
if ($chk[cf_post_date]) $sql .= ", cf_post_date = '$cf_post_date' ";
if ($chk[cf_post_hit]) $sql .= ", cf_post_hit = '$cf_post_hit' ";
if ($chk[cf_list_good]) $sql .= ", cf_list_good = '$cf_list_good' ";
if ($chk[cf_post_num]) $sql .= ", cf_post_num = '$cf_post_num' ";
if ($chk[cf_img_1_noview]) $sql .= ", cf_img_1_noview = '$cf_img_1_noview' ";
if ($chk[cf_only_one]) $sql .= ", cf_only_one = '$cf_only_one' ";
if ($chk[cf_must_notice]) {
    $sql .= ", cf_must_notice = '$cf_must_notice' ";
    $sql .= ", cf_must_notice_read = '$cf_must_notice_read' ";
    $sql .= ", cf_must_notice_comment = '$cf_must_notice_comment' ";
    $sql .= ", cf_must_notice_down = '$cf_must_notice_down' ";
}
if ($chk[cf_comment_good]) $sql .= ", cf_comment_good = '$cf_comment_good' ";
if ($chk[cf_comment_nogood]) $sql .= ", cf_comment_nogood = '$cf_comment_nogood' ";
if ($chk[cf_comment_best]) $sql .= ", cf_comment_best = '$cf_comment_best' ";
if ($chk[cf_comment_best_limit]) $sql .= ", cf_comment_best_limit = '$cf_comment_best_limit' ";
if ($chk[cf_iframe_level]) $sql .= ", cf_iframe_level = '$cf_iframe_level' ";
if ($chk[cf_icon_level]) {
    $sql .= ", cf_icon_level = '$cf_icon_level' ";
    $sql .= ", cf_icon_level_point = '$cf_icon_level_point' ";
}
if ($chk[cf_change_image_size]) {
    $sql .= ", cf_change_image_size = '$cf_change_image_size' ";
    $sql .= ", cf_change_image_size_level = '$cf_change_image_size_level' ";
}
if ($chk[cf_good_point]) {
    $sql .= ", cf_good_point = '$cf_good_point' ";
    $sql .= ", cf_good_re_point = '$cf_good_re_point' ";
}
if ($chk[cf_nogood_point]) {
    $sql .= ", cf_nogood_point = '$cf_nogood_point' ";
    $sql .= ", cf_nogood_re_point = '$cf_nogood_re_point' ";
}
if ($chk[cf_comment_good_point]) {
    $sql .= ", cf_comment_good_point = '$cf_comment_good_point' ";
    $sql .= ", cf_comment_good_re_point = '$cf_comment_good_re_point' ";
}
if ($chk[cf_comment_nogood_point]) {
    $sql .= ", cf_comment_nogood_point = '$cf_comment_nogood_point' ";
    $sql .= ", cf_comment_nogood_re_point = '$cf_comment_nogood_re_point' ";
}
if ($chk[cf_good_days]) $sql .= ", cf_good_days = '$cf_good_days' ";
if ($chk[cf_good_cancel_days]) $sql .= ", cf_good_cancel_days = '$cf_good_cancel_days' ";
if ($chk[cf_good_count]) $sql .= ", cf_good_count = '$cf_good_count' ";
if ($chk[cf_social_commerce]) $sql .= ", cf_social_commerce = '$cf_social_commerce' ";
if ($chk[cf_google_map]) $sql .= ", cf_google_map = '$cf_google_map' ";
if ($chk[cf_contents_shop]) {
    $sql .= ", cf_contents_shop = '$cf_contents_shop' ";
    $sql .= ", cf_contents_shop_download_count = '$cf_contents_shop_download_count' ";
    $sql .= ", cf_contents_shop_download_day = '$cf_contents_shop_download_day' ";
    $sql .= ", cf_contents_shop_fix = '$cf_contents_shop_fix' ";
    $sql .= ", cf_contents_shop_max = '$cf_contents_shop_download_day' ";
    $sql .= ", cf_contents_shop_min = '$cf_contents_shop_min' ";
}
if ($chk[cf_contents_shop_uploader]) {
    $sql .= ", cf_contents_shop_uploader = '$cf_contents_shop_uploader' ";
    $sql .= ", cf_contents_shop_uploader_cash = '$cf_contents_shop_uploader_cash' ";
}
if ($chk[cf_contents_shop_write]) {
    $sql .= ", cf_contents_shop_write = '$cf_contents_shop_write' ";
    $sql .= ", cf_contents_shop_write_cash = '$cf_contents_shop_write_cash' ";
}
if ($chk[cf_admin_dhtml]) $sql .= ", cf_admin_dhtml = '$cf_admin_dhtml' ";
if ($chk[cf_admin_dhtml_comment]) $sql .= ", cf_admin_dhtml_comment = '$cf_admin_dhtml_comment' ";
if ($chk[cf_comment_secret]) $sql .= ", cf_comment_secret = '$cf_comment_secret' ";
if ($chk[cf_comment_secret_no]) $sql .= ", cf_comment_secret_no = '$cf_comment_secret_no' ";
if ($chk[cf_replace_word]) $sql .= ", cf_replace_word = '$cf_replace_word' ";
if ($chk[cf_view_good]) $sql .= ", cf_view_good = '$cf_view_good' ";
if ($chk[cf_name_title]) $sql .= ", cf_name_title = '$cf_name_title' ";
if ($chk[cf_attach_count]) $sql .= ", cf_attach_count = '$cf_attach_count' ";
if ($chk[cf_related_table]) $sql .= ", cf_related_table = '$cf_related_table' ";
if ($chk[cf_latest_table]) $sql .= ", cf_latest_table = '$cf_latest_table' ";
if ($chk[cf_anonymous]) $sql .= ", cf_anonymous = '$cf_anonymous' ";
if ($chk[cf_write_notice]) $sql .= ", cf_write_notice = '$cf_write_notice' ";
if ($chk[cf_css]) $sql .= ", cf_css = '$cf_css' ";
if ($chk[cf_exif]) $sql .= ", cf_exif = '$cf_exif' ";
if ($chk[cf_no_img_ext]) $sql .= ", cf_no_img_ext = '$cf_no_img_ext' ";
if ($chk[cf_print]) $sql .= ", cf_print = '$cf_print' ";
if ($chk[cf_umz]) $sql .= ", cf_umz = '$cf_umz' ";
if ($chk[cf_umz]) $sql .= ", cf_umz2 = '$cf_umz2' ";
if ($chk[cf_shorten]) $sql .= ", cf_shorten = '$cf_shorten' ";
if ($chk[cf_include_view_top]) $sql .= ", cf_include_view_top = '$cf_include_view_top' ";
if ($chk[cf_include_view_head]) $sql .= ", cf_include_view_head = '$cf_include_view_head' ";
if ($chk[cf_include_view]) $sql .= ", cf_include_view = '$cf_include_view' ";
if ($chk[cf_include_view_tail]) $sql .= ", cf_include_view_tail = '$cf_include_view_tail' ";
if ($chk[cf_include_file_head]) $sql .= ", cf_include_file_head = '$cf_include_file_head' ";
if ($chk[cf_include_file_tail]) $sql .= ", cf_include_file_tail = '$cf_include_file_tail' ";
if ($chk[cf_include_head]) $sql .= ", cf_include_head = '$cf_include_head' ";
if ($chk[cf_include_tail]) $sql .= ", cf_include_tail = '$cf_include_tail' ";
if ($chk[cf_include_list_main]) $sql .= ", cf_include_list_main = '$cf_include_list_main' ";
if ($chk[cf_include_comment_main]) $sql .= ", cf_include_comment_main = '$cf_include_comment_main' ";
if ($chk[cf_subject_style]) {
    $sql .= ", cf_subject_style = '$cf_subject_style' ";
    $sql .= ", cf_subject_style_level = '$cf_subject_style_level' ";
    $sql .= ", cf_subject_style_color_default = '$cf_subject_style_color_default' ";
    $sql .= ", cf_subject_style_color_picker = '$cf_subject_style_color_picker' ";
}
if ($chk[cf_guploader]) $sql .= ", cf_guploader = '$cf_guploader' ";
if ($chk[cf_under_construction]) $sql .= ", cf_under_construction = '$cf_under_construction' ";
if ($chk[cf_no_delete]) $sql .= ", cf_no_delete = '$cf_no_delete' ";
if ($chk[cf_vote]) $sql .= ", cf_vote = '$cf_vote' ";
if ($chk[cf_vote_level]) $sql .= ", cf_vote_level = '$cf_vote_level' ";
if ($chk[cf_vote_join_level]) $sql .= ", cf_vote_join_level = '$cf_vote_join_level' ";
if ($chk[cf_quiz]) {
    $sql .= ", cf_quiz = '$cf_quiz' ";
    $sql .= ", cf_quiz_level = '$cf_quiz_level' ";
    $sql .= ", cf_quiz_join_level = '$cf_quiz_join_level' ";
}
if ($chk[cf_collect]) $sql .= ", cf_collect = '$cf_collect' ";
if ($chk[cf_read_level]) {
    $sql .= ", cf_read_level = '$cf_read_level' ";
    $sql .= ", cf_read_level_own = '$cf_read_level_own' ";
}
if ($chk[cf_talent_market]) {
    $sql .= ", cf_talent_market = '$cf_talent_market' ";
    $sql .= ", cf_talent_market_commission = '$cf_talent_market_commission' ";
    $sql .= ", cf_talent_market_min = '$cf_talent_market_min' ";
    $sql .= ", cf_talent_market_min_point = '$cf_talent_market_min_point' ";
    $sql .= ", cf_talent_market_max = '$cf_talent_market_max' ";
    $sql .= ", cf_talent_market_max_point = '$cf_talent_market_max_point' ";
    $sql .= ", cf_talent_market_app = '$cf_talent_market_app' ";
}
/*if ($chk[cf_preview_level]) {
    $sql .= ", cf_preview_level = '$cf_preview_level' ";
    $sql .= ", cf_preview_size = '$cf_preview_size' ";
}*/
if ($chk[cf_reward]) $sql .= ", cf_reward = '$cf_reward' ";
if ($chk[cf_singo_after]) {
    $sql .= ", cf_singo_after = '$cf_singo_after' ";
    $sql .= ", cf_singo_number = '$cf_singo_number' ";
    $sql .= ", cf_singo_id_block = '$cf_singo_id_block' ";
    $sql .= ", cf_singo_write_block = '$cf_singo_write_block' ";
    $sql .= ", cf_singo_write_secret = '$cf_singo_write_secret' ";
    $sql .= ", cf_singo_level = '$cf_singo_level' ";
    $sql .= ", cf_singo_writer = '$cf_singo_writer' ";
}
if ($chk[cf_write_register]) {
    $sql .= ", cf_write_point = '$cf_write_point' ";
    $sql .= ", cf_write_register = '$cf_write_register' ";
}
if ($chk[cf_write_day]) {
    $sql .= ", cf_write_day = '$cf_write_day' ";
    $sql .= ", cf_write_day_count = '$cf_write_day_count' ";
}
if ($chk[cf_comment_register]) {
    $sql .= ", cf_comment_point = '$cf_comment_point' ";
    $sql .= ", cf_comment_register = '$cf_comment_register' ";
}
if ($chk[cf_comment_day]) {
    $sql .= ", cf_comment_day = '$cf_comment_day' ";
    $sql .= ", cf_comment_day_count = '$cf_comment_day_count' ";
    $sql .= ", cf_comment_write_count = '$cf_comment_write_count' ";
}
if ($chk[cf_read_register]) {
    $sql .= ", cf_read_point = '$cf_read_point' ";
    $sql .= ", cf_read_register = '$cf_read_register' ";
}
if ($chk[cf_good_graph]) $sql .= ", cf_good_graph = '$cf_good_graph' ";
//if ($chk[cf_star]) $sql .= ", cf_star = '$cf_star' ";

if ($chk[cf_watermark_use]) {
    $sql .= ", cf_watermark_use = '$cf_watermark_use' ";
    $sql .= ", cf_watermark_use_thumb = '$cf_watermark_use_thumb' ";
}
if ($chk[cf_watermark_path]) $sql .= ", cf_watermark_path = '$cf_watermark_path' ";
if ($chk[cf_watermark_position]) $sql .= ", cf_watermark_position = '$cf_watermark_position' ";
if ($chk[cf_watermark_transparency]) $sql .= ", cf_watermark_transparency = '$cf_watermark_transparency' ";
if ($chk[cf_kcb_id]) $sql .= ", cf_kcb_id = '$cf_kcb_id' ";
if ($chk[cf_kcb_type]) $sql .= ", cf_kcb_type = '$cf_kcb_type' ";
if ($chk[cf_kcb_list]) {
    $sql .= ", cf_kcb_list = '$cf_kcb_list' ";
    $sql .= ", cf_kcb_read = '$cf_kcb_read' ";
    $sql .= ", cf_kcb_write = '$cf_kcb_write' ";
    $sql .= ", cf_kcb_comment = '$cf_kcb_comment' ";
}
if ($chk[cf_kcb_year]) $sql .= ", cf_kcb_year = '$cf_kcb_year' ";
if ($chk[cf_kcb_post]) {
    $sql .= ", cf_kcb_post = '$cf_kcb_post' ";
    $sql .= ", cf_kcb_post_level = '$cf_kcb_post_level' ";
}
if ($chk[cf_lucky_writing_chance]) {
    $sql .= ", cf_lucky_writing_ment = '$cf_lucky_writing_ment' ";
    $sql .= ", cf_lucky_writing_comment = '$cf_lucky_writing_comment' ";
    $sql .= ", cf_lucky_writing_chance = '$cf_lucky_writing_chance' ";
    $sql .= ", cf_lucky_writing_point_start = '$cf_lucky_writing_point_start' ";
    $sql .= ", cf_lucky_writing_point_end = '$cf_lucky_writing_point_end' ";
    $sql .= ", cf_lucky_writing_comment_chance = '$cf_lucky_writing_comment_chance' ";
    $sql .= ", cf_lucky_writing_comment_point_start = '$cf_lucky_writing_comment_point_start' ";
    $sql .= ", cf_lucky_writing_comment_point_end = '$cf_lucky_writing_comment_point_end' ";
}
$sql .= " where gr_id = '$gr_id' ";
$def .= " where gr_id = '$gr_id' ";
sql_query($sql);

//mw_basic_write_config_file($gr_id);

if ($sql != $def) {
    $sql = "select bo_table from $g4[board_table] where gr_id = '$gr_id'";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $config_file = "$mw_basic_config_path/$row[bo_table]";

        $sql = "select * from $mw[basic_config_table] where bo_table = '$row[bo_table]'";
        $contents = sql_fetch($sql, false);
        $contents = serialize($contents);
        $contents = base64_encode($contents);

        $f = fopen($config_file, "w");
        fwrite($f, $contents);
        fclose($f);
        @chmod($config_file, 0600);
    }
} else {
    $config_file = "$mw_basic_config_path/$bo_table";

    $sql = "select * from $mw[basic_config_table] where bo_table = '$bo_table'";
    $contents = sql_fetch($sql, false);
    $contents = serialize($contents);
    $contents = base64_encode($contents);

    $f = fopen($config_file, "w");
    fwrite($f, $contents);
    fclose($f);
    @chmod($config_file, 0600);
}

alert("설정을 저장하였습니다.", "mw.config.php?bo_table=$bo_table&tn=$tn");
