<?
/**
 * Bechu basic skin for gnuboard4
 *
 * copyright (c) 2008 Choi Jae-Young <www.miwit.com>
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

define("_MW_BOARD_", TRUE);

// 디렉토리 생성
function mw_mkdir($path, $permission=0707) {
    if (is_dir($path)) return;
    if (file_exists($path)) @unlink($path);

    @mkdir($path, $permission);
    @chmod($path, $permission);

    // 디렉토리에 있는 파일의 목록을 보이지 않게 한다.
    $file = $path . "/index.php";
    $f = @fopen($file, "w");
    @fwrite($f, "");
    @fclose($f);
    @chmod($file, 0606);
}

// 관련글 얻기.. 080429, curlychoi
function mw_related($related, $field="wr_id, wr_subject, wr_content, wr_datetime, wr_comment")
{
    global $bo_table, $write_table, $g4, $wr_id, $mw_basic;

    if (!trim($related)) return;

    $bo_table2 = $bo_table;
    $write_table2 = $write_table;

    if (trim($mw_basic[cf_related_table])) {
        $bo_table2 = $mw_basic[cf_related_table];
        $write_table2 = "$g4[write_prefix]$bo_table2";
    }

    $sql_where = "";
    $related = explode(",", $related);
    foreach ($related as $rel) {
        $rel = trim($rel);
        if ($rel) {
            $rel = addslashes($rel);
            if ($sql_where) {
                $sql_where .= " or ";
            }
            $sql_where .= " (instr(wr_subject, '$rel') or instr(wr_content, '$rel')) ";
        }
    }
    if (!trim($mw_basic[cf_related_table]))
        $sql_where .= " and wr_id <> '$wr_id' ";

    $sql = "select $field from $write_table2 where wr_is_comment = 0 and ($sql_where) order by wr_num ";
    $qry = sql_query($sql, false);

    $list = array();
    $i = 0;
    while ($row = sql_fetch_array($qry)) {
        $row[href] = "$g4[bbs_path]/board.php?bo_table=$bo_table2&wr_id=$row[wr_id]";
        $row[comment] = $row[wr_comment] ? "<span class='comment'>($row[wr_comment])</span>" : "";
        $row[subject] = get_text($row[wr_subject]);
        $row[subject] = mw_reg_str($row[subject]);
        $list[$i] = $row;
        if (++$i >= $mw_basic[cf_related]) {
            break;
        }
    }
    return $list;
}

// 관련글 얻기.. 080429, curlychoi
function mw_view_latest($field="wr_id, wr_subject, wr_content, wr_datetime, wr_comment")
{
    global $bo_table, $write_table, $g4, $wr_id, $write, $mw_basic;

    $bo_table2 = $bo_table;
    $write_table2 = $write_table;

    if (trim($mw_basic[cf_latest_table])) {
        $bo_table2 = $mw_basic[cf_latest_table];
        $write_table2 = "$g4[write_prefix]$bo_table2";
    }

    $sql = "select $field from $write_table2 where wr_is_comment = 0 and wr_id <> '$wr_id' and mb_id = '$write[mb_id]' order by wr_num limit $mw_basic[cf_latest] ";
    $qry = sql_query($sql, false);

    $list = array();
    $i = 0;
    for ($i=0; $row=sql_fetch_array($qry); $i++) {
        $row[href] = "$g4[bbs_path]/board.php?bo_table=$bo_table2&wr_id=$row[wr_id]";
        //$row[comment] = $row[wr_comment] ? "<span class='comment'>($row[wr_comment])</span>" : "";
        $row[comment] = $row[wr_comment] ? "<span class='comment'>+$row[wr_comment]</span>" : "";
        $row[subject] = get_text($row[wr_subject]);
        $row[subject] = mw_reg_str($row[subject]);
        $list[$i] = $row;
    }
    return $list;
}

function mw_thumbnail_keep($size, $set_width, $set_height) {
    global $mw_basic;

    if (!$mw_basic[cf_resize_base])
        $mw_basic[cf_resize_base] = 'long';

    if ($mw_basic[cf_resize_base] == 'long')
    {
        if ($size[0] > $size[1]) {
            @$rate = $set_width / $size[0];
            $get_width = $set_width;
            $get_height = (int)($size[1] * $rate);
        } else {
            @$rate = $set_width / $size[1];
            $get_height = $set_width;
            $get_width = (int)($size[0] * $rate);
        }
    }
    else if ($mw_basic[cf_resize_base] == 'width') {
        @$rate = $set_width / $size[0];
        $get_width = $set_width;
        $get_height = (int)($size[1] * $rate);
    }
    else if ($mw_basic[cf_resize_base] == 'height') {
        @$rate = $set_height / $size[1];
        $get_height = $set_height;
        $get_width = (int)($size[0] * $rate);
    }
    return array($get_width, $get_height);
}

// 썸네일 생성.. 080408, curlychoi
function mw_make_thumbnail($set_width, $set_height, $source_file, $thumbnail_file='', $keep=false)
{
    global $g4, $mw_basic;

    if (!$set_width && !$set_height) return;

    if (!$thumbnail_file)
        $source_file = $thumbnail_file;

    $size = @getimagesize($source_file);

    switch ($size[2]) {
        case 1: $source = @imagecreatefromgif($source_file); break;
        case 2: $source = @imagecreatefromjpeg($source_file); break;
        case 3: $source = @imagecreatefrompng($source_file); break;
        default: return false;
    }

    if ($keep)
    {
	$keep_size = mw_thumbnail_keep($size, $set_width, $set_height);
	$set_width = $get_width = $keep_size[0];
	$set_height = $get_height = $keep_size[1];
    }
    else
    {
        $rate = $set_width / $size[0];
        $get_width = $set_width;
        $get_height = (int)($size[1] * $rate); 

        $temp_h = (int)($set_height / $set_width * $size[0]);
        $src_y = (int)(($size[1] - $temp_h) / 2);

        if ($get_height < $set_height) {
            //$get_width = $set_width + $set_height - $get_height;
            //$get_height = $set_height;
            $rate = $set_height / $size[1];
            $get_height = $set_height;
            $get_width = (int)($size[0] * $rate); 

            $src_y = 0;
            $temp_w = (int)($set_width / $set_height * $size[1]);
            $src_x = (int)(($size[0] - $temp_w) / 2);
        }
    }

    $target = @imagecreatetruecolor($set_width, $set_height);
    $white = @imagecolorallocate($target, 255, 255, 255);
    @imagefilledrectangle($target, 0, 0, $set_width, $set_height, $white);
    @imagecopyresampled($target, $source, 0, 0, $src_x, $src_y, $get_width, $get_height, $size[0], $size[1]);

    if ($source_file != $thumbnail_file && $mw_basic[cf_watermark_use_thumb]
        && file_exists("$g4[bbs_path]/$mw_basic[cf_watermark_path]")) { // watermark
        mw_watermark($target, $set_width, $set_height
            , "$g4[bbs_path]/$mw_basic[cf_watermark_path]"
            , $mw_basic[cf_watermark_position]
            , $mw_basic[cf_watermark_transparency]);
    }

    if (!$mw_basic[cf_resize_quality])
        $mw_basic[cf_resize_quality] = 100;

    @imagejpeg($target, $thumbnail_file, $mw_basic[cf_resize_quality]);
    @chmod($thumbnail_file, 0606);

    @imagedestroy($target);
    @imagedestroy($source);
}

function mw_watermark($target, $tw, $th, $source, $position, $transparency=100)
{
    global $mw_basic;

    $wf = $source;
    $ws = @getimagesize($wf);

    switch ($ws[2]) {
        case 1: $wi = @imagecreatefromgif($wf); break;
        case 2: $wi = @imagecreatefromjpeg($wf); break;
        case 3: $wi = @imagecreatefrompng($wf); break;
        default: $wi = "";
    }
    switch($position) {
        case "center":
            $wx = (int)($tw/2 - $ws[0]/2);
            $wy = (int)($th/2 - $ws[1]/2);
            break;
        case "left_top":
            $wx = $wy = 0;
            break;
        case "left_bottom":
            $wx = 0;
            $wy = $th - $ws[1];
            break;
        case "right_top":
            $wx = $tw - $ws[0];
            $wy = 0;
            break;
        case "right_bottom":
            $wx = $tw - $ws[0];
            $wy = $th - $ws[1];
            break;
        default:
            $wx = (int)($tw/2 - $ws[0]/2);
            $wy = (int)($th/2 - $ws[1]/2);
            break;
    }
    if ($ws[2] == 3) {
        imagealphablending($wi, TRUE);
        imagealphablending($target, TRUE);
        imagecopy($target, $wi, $wx, $wy, 0, 0, $ws[0], $ws[1]);
    } else {
        imagecopymerge($target, $wi, $wx, $wy, 0, 0, $ws[0], $ws[1], $transparency);
    }
    @imagedestroy($wi);
}

function mw_watermark_file($source_file)
{
    global $watermark_path, $mw_basic, $g4;

    if (!file_exists($source_file)) return;

    $pathinfo = pathinfo($source_file);
    $basename = md5(basename($source_file)).'.'.$pathinfo[extension];
    $watermark_file = "$watermark_path/$basename";

    if (file_exists($watermark_file)) return $watermark_file;

    $size = @getimagesize($source_file);
    switch ($size[2]) {
        case 1: $source = @imagecreatefromgif($source_file); break;
        case 2: $source = @imagecreatefromjpeg($source_file); break;
        case 3: $source = @imagecreatefrompng($source_file); break;
        default: return;
    }

    $target = @imagecreatetruecolor($size[0], $size[1]);
    $white = @imagecolorallocate($target, 255, 255, 255);
    @imagefilledrectangle($target, 0, 0, $size[0], $size[1], $white);
    @imagecopyresampled($target, $source, 0, 0, 0, 0, $size[0], $size[1], $size[0], $size[1]);

    mw_watermark($target, $size[0], $size[1]
        , $mw_basic[cf_watermark_path]
        , $mw_basic[cf_watermark_position]
        , $mw_basic[cf_watermark_transparency]);

    @imagejpeg($target, $watermark_file, 100);
    @chmod($watermark_file, 0606);
    @imagedestroy($source);
    @imagedestroy($target);

    return $watermark_file;
}

// 첨부파일의 첫번째 파일을 가져온다.. 080408, curlychoi
// 이미지파일을 가져오는 인수 추가.. 080414, curlychoi
function mw_get_first_file($bo_table, $wr_id, $is_image=false)
{
    global $g4;
    $sql_image = "";
    if ($is_image) $sql_image = " and bf_width > 0 ";
    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' $sql_image order by bf_no limit 1";
    $row = sql_fetch($sql);
    return $row;
}

// 핸드폰번호 형식으로 return
function mw_get_hp($hp, $hyphen=1)
{
    if (!mw_is_hp($hp)) return '';

    if ($hyphen) $preg = "$1-$2-$3"; else $preg = "$1$2$3";

    $hp = str_replace('-', '', trim($hp));
    $hp = preg_replace("/^(01[016789])([0-9]{3,4})([0-9]{4})$/", $preg, $hp);

    return $hp;
}

// 핸드폰번호 여부
function mw_is_hp($hp)
{
    $hp = str_replace('-', '', trim($hp));
    if (preg_match("/^(0[17][016789])([0-9]{3,4})([0-9]{4})$/", $hp))
        return true;
    else
        return false;
}

// 분류 옵션을 얻음
function mw_get_category_option($bo_table='')
{
    global $g4, $board;

    $arr = explode("|", $board[bo_category_list]); // 구분자가 , 로 되어 있음
    $str = "";
    for ($i=0; $i<count($arr); $i++)
        if (trim($arr[$i]))
            $str .= "<option value='".urlencode($arr[$i])."'>$arr[$i]</option>\n";

    return $str;
}

function mw_set_sync_tag($content) {
    global $member;
    preg_match_all("/<([^>]*)</iUs", $content, $matchs);
    for ($i=0, $max=count($matchs[0]); $i<$max; $i++) {
	$pos = strpos($content, $matchs[0][$i]);
	$len = strlen($matchs[0][$i]);
	$content = substr($content, 0, $pos + $len - 1) . ">" . substr($content, $pos + $len - 1, strlen($content));
    }
 
    $content = mw_get_sync_tag($content, "div");
    $content = mw_get_sync_tag($content, "table");
    $content = mw_get_sync_tag($content, "font");
    return $content;
}

// html 태그 갯수 맞추기
function mw_get_sync_tag($content, $tag) {
    $tag = strtolower($tag);
    $res = strtolower($content);

    $open  = substr_count($res, "<$tag");
    $close = substr_count($res, "</$tag");

    if ($open > $close) {

        $gap = $open - $close;
        for($i=0; $i<$gap; $i++)
            $content .= "</$tag>";

    } else {

        $gap = $close - $open;
        for($i=0; $i<$gap; $i++)
            $content = "<$tag>".$content;
    }

    return $content;
}

// 엄지 짧은링크 얻기
function umz_get_url($url) {
    global $mw_basic;
    $surl = $mw_basic[cf_umz2];
    if (!$surl)
        $surl = "umz.kr";
    $url2 = urlencode($url);
    $fp = fsockopen ($surl, 80, $errno, $errstr, 30);
    if (!$fp) return false;
    fputs($fp, "POST /plugin/shorten/update.php?url=$url2 HTTP/1.0\r\n");
    fputs($fp, "Host: $surl:80\r\n");
    fputs($fp, "\r\n");
    while (trim($buffer = fgets($fp,1024)) != "") $header .= $buffer;
    while (!feof($fp)) $buffer .= fgets($fp,1024);
    fclose($fp);
    $ret = trim($buffer);
    if (substr($ret, 0, strlen($surl)+7) != "http://$surl") return '';
    return $ret;
}

// euckr -> utf8 
if (!function_exists("set_utf8")) {
function set_utf8($str)
{
    if (!is_utf8($str))
        $str = convert_charset('cp949', 'utf-8', $str);

    $str = trim($str);

    return $str;
}}

// utf8 -> euckr 
if (!function_exists("set_euckr")) {
function set_euckr($str)
{
    if (is_utf8($str))
        $str = convert_charset('utf-8', 'cp949', $str);

    $str = trim($str);

    return $str;
}}


// Charset 을 변환하는 함수 
if (!function_exists("convert_charset")) {
function convert_charset($from_charset, $to_charset, $str) {
    if( function_exists('iconv') )
        return iconv($from_charset, $to_charset, $str);
    elseif( function_exists('mb_convert_encoding') )
        return mb_convert_encoding($str, $to_charset, $from_charset);
    else
        die("Not found 'iconv' or 'mbstring' library in server.");
}}

// 텍스트가 utf-8 인지 검사하는 함수 
if (!function_exists("is_utf8")) {
function is_utf8($string) {

  // From http://w3.org/International/questions/qa-forms-utf-8.html
  return preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E]            # ASCII
      | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
      |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
      | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
      |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
      |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
      | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
      |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
 )*$%xs', $string);
}}

// syntax highlight 
function _preg_callback($m)
{
    //$str = str_replace(array("<br/>", "&nbsp;"), array("\n", " "), $m[1]);
    $str = $m[1];
    $str = preg_replace("/<br[\/]{0,1}>/i", "\n", $str);
    $str = preg_replace("/&nbsp;/i", " ", $str);
    $str = preg_replace("/<div>/i", "", $str);
    $str = preg_replace("/<\/div>/i", "\n", $str);
    return "<pre class='brush:php;'>$str</pre>";
}

function mw_get_level($mb_id) {
    global $icon_level_mb_id;
    global $icon_level_mb_point;
    global $mw_basic;
    $point = 0;
    if (!is_array($icon_level_mb_id)) $icon_level_mb_id = array();
    if (!is_array($icon_level_mb_point)) $icon_level_mb_point = array();
    if (!in_array($mb_id, $icon_level_mb_id)) {
        $icon_level_mb_id[] = $mb_id;
        $mb = get_member($mb_id, "mb_point");
        $icon_level_mb_point[$mb_id] = $mb[mb_point];
        $point = $mb[mb_point];
    } else {
        $point = $icon_level_mb_point[$mb_id];
    }
    $level = intval($point/$mw_basic[cf_icon_level_point]);
    if ($level > 98) $level = 98;
    if ($level < 0) $level = 0;
    return $level;
}

// 코멘트 첨부된 파일을 얻는다. (배열로 반환)
function get_comment_file($bo_table, $wr_id)
{
    global $g4, $mw, $qstr;

    $file["count"] = 0;
    $sql = " select * from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' order by bf_no ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $no = $row[bf_no];
        $file[$no][href] = "./download.php?bo_table=$bo_table&wr_id=$wr_id&no=$no" . $qstr;
        $file[$no][download] = $row[bf_download];
        // 4.00.11 - 파일 path 추가
        $file[$no][path] = "$g4[path]/data/file/$bo_table";
        //$file[$no][size] = get_filesize("{$file[$no][path]}/$row[bf_file]");
        $file[$no][size] = get_filesize($row[bf_filesize]);
        //$file[$no][datetime] = date("Y-m-d H:i:s", @filemtime("$g4[path]/data/file/$bo_table/$row[bf_file]"));
        $file[$no][datetime] = $row[bf_datetime];
        $file[$no][source] = $row[bf_source];
        $file[$no][bf_content] = $row[bf_content];
        $file[$no][content] = get_text($row[bf_content]);
        //$file[$no][view] = view_file_link($row[bf_file], $file[$no][content]);
        $file[$no][view] = view_file_link($row[bf_file], $row[bf_width], $row[bf_height], $file[$no][content]);
        $file[$no][file] = $row[bf_file];
        // prosper 님 제안
        //$file[$no][imgsize] = @getimagesize("{$file[$no][path]}/$row[bf_file]");
        $file[$no][image_width] = $row[bf_width] ? $row[bf_width] : 640;
        $file[$no][image_height] = $row[bf_height] ? $row[bf_height] : 480;
        $file[$no][image_type] = $row[bf_type];
        $file["count"]++;
    }

    return $file;
}

// 호칭
function get_name_title($name, $wr_name) {
    global $mw_basic;
    if (strlen(trim($mw_basic[cf_name_title]))) {
        $name = str_replace("<span class='member'>{$wr_name}</span>", "<span class='member'>{$wr_name}{$mw_basic[cf_name_title]}</span>", $name);
    }
    return $name;
}

// 에디터 첨부 이미지 목록 가져오기
function mw_get_editor_image($data)
{
    global $g4, $watermark_path;

    $editor_image = $ret = array();

    $url = $g4[url];
    $url = preg_replace("(\/)", "\\\/", $url);
    $url = preg_replace("(\.)", "\.", $url);

    $ext = "src=\"({$url}\/data\/geditor[^\"]+)\"";
    preg_match_all("/$ext/iUs", $data, $matchs);
    for ($j=0; $j<count($matchs[1]); $j++) {
        $editor_image[] = $matchs[1][$j];
    }

    $ext = "src=\"({$url}\/data\/mw\.cheditor[^\"]+)\"";
    preg_match_all("/$ext/iUs", $data, $matchs);
    for ($j=0; $j<count($matchs[1]); $j++) {
        $editor_image[] = $matchs[1][$j];
    }

    $ext = "src=\"({$url}\/data\/{$g4[cheditor4]}[^\"]+)\"";
    preg_match_all("/$ext/iUs", $data, $matchs);
    for ($j=0; $j<count($matchs[1]); $j++) {
        $editor_image[] = $matchs[1][$j];
    }

    for ($j=0, $m=count($editor_image); $j<$m; $j++) {
        $match = $editor_image[$j];
        if (strstr($match, $g4[url])) { // 웹에디터로 첨부한 이미지 뿐 아니라 다양한 상황을 고려함.
            $path = str_replace($g4[url], "..", $match);
        } elseif (substr($match, 0, 1) == "/") {
            $path = $_SERVER[DOCUMENT_ROOT].$match;
        } else {
            $path = $match;
        }
        $ret[http_path][$j] = $match;
        $ret[local_path][$j] = $path;
    }
    return $ret;
}

// 에디터 이미지 워터마크 생성
function mw_create_editor_image_watermark($data)
{
    global $g4, $watermark_path;

    $editor_image = mw_get_editor_image($data);

    for ($j=0, $m=count($editor_image[local_path]); $j<$m; $j++) {
        $match = $editor_image[http_path][$j];
        $path = $editor_image[local_path][$j];
        $size = @getimagesize($path);
        if ($size[0] > 0) {
            $watermark_file = mw_watermark_file($path);
            $data = str_replace($match, $watermark_file, $data);
        }
    }
    return $data;
}

// 에디터 이미지 및 워터마크 삭제
function mw_delete_editor_image($data)
{
    global $g4, $watermark_path;

    $editor_image = mw_get_editor_image($data);

    for ($j=0, $m=count($editor_image[local_path]); $j<$m; $j++) {
        $path = $editor_image[local_path][$j];
        $size = @getimagesize($path);
        if ($size[0] > 0) {
            $watermark_file = "$watermark_path/".basename($path);
            if (file_exists($path)) @unlink($path); // 에디터 이미지 삭제
            if (file_exists($watermark_file)) @unlink($watermark_file); // 에디터 워터마크 삭제
        }
    }
}

// 팝업공지
function mw_board_popup($view, $html=0)
{
    global $is_admin, $bo_table, $g4, $board_skin_path, $mw_basic, $board;

    $dialog_id = "mw_board_popup_$view[wr_id]";

    $board[bo_image_width] = 550;

    // 파일 출력
    ob_start();
    $cf_img_1_noview = $mw_basic[cf_img_1_noview];
    for ($i=0; $i<=$view[file][count]; $i++) {
        if ($cf_img_1_noview && $view[file][$i][view]) {
            $cf_img_1_noview = false;
            continue;
        }
        if ($view[file][$i][view])
        {
            // 이미지 크기 조절
            if ($board[bo_image_width] < $view[file][$i][image_width]) {
                $img_width = $board[bo_image_width];
            } else {
                $img_width = $view[file][$i][image_width];
            }
            $view[file][$i][view] = str_replace("<img", "<img width=\"{$img_width}\"", $view[file][$i][view]);

            // 워터마크 이미지 출력
            if ($mw_basic[cf_watermark_use]) {
                preg_match("/src='([^']+)'/iUs", $view[file][$i][view], $match);
                $watermark_file = mw_watermark_file($match[1]);
                $view[file][$i][view] = str_replace($match[1], $watermark_file, $view[file][$i][view]);
            }

            echo $view[file][$i][view] . "<br/><br/>";
        }
    }
    $file_viewer = ob_get_contents();
    ob_end_clean();

    $html = 0;
    if (strstr($view['wr_option'], "html1"))
        $html = 1;
    else if (strstr($view['wr_option'], "html2"))
        $html = 2;

    $view[content] = conv_content($view[wr_content], $html);
    $view[rich_content] = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "view_image(\$view, '\\1', '\\2')", $view[content]);
    $view[rich_content] = mw_reg_str($view[rich_content]);
    $view[rich_content] = bc_code($view[rich_content]);

    $subject = get_text($view[subject]);
    $subject = mw_reg_str($subject);
    $subject = bc_code($subject);
    $content = $file_viewer.$view[rich_content];

    set_session("ss_popup_token", $token = uniqid(time()));

    $add_script = "";
    if ($is_admin && $view[wr_id]) {
        $add_script = <<<HEREDOC
            "팝업내림": function () {
                var q = confirm("정말로 팝업공지를 내리시겠습니까?")
                if (q) {
                    $.get("$board_skin_path/mw.proc/mw.popup.php?bo_table=$bo_table&wr_id=$view[wr_id]&token=$token", function (ret) {
                        alert(ret);
                    });
                }
            },
HEREDOC;
    }
    if ($_COOKIE[$dialog_id]) return false;
    
    echo <<<HEREDOC
        <div id="dialog-message-$view[wr_id]" class="dialog-content" title="$subject">
            <div>$content</div>
        </div>
        <script type="text/javascript">
        $(function() {
            $("#dialog-message-$view[wr_id]").dialog({
                modal: true,
                minWidth: 600,
                minHeight: 300,
                buttons: {
                    $add_script
                    "24시간 동안 창을 띄우지 않습니다.": function () {
                        set_cookie("mw_board_popup_$view[wr_id]", "1", 24, "$g4[cookie_domain]");
                        $(this).dialog("close");
                    },
                    Ok: function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        </script>
        <style type="text/css">
        .ui-dialog .ui-dialog-buttonpane button { font-size:.8em; }
        </style>
HEREDOC;
}

function is_okname()
{
    global $g4, $mw, $member, $mw_basic, $is_admin;

    if ($is_admin == 'super') return true;

    set_session("ss_ipin_bo_table", "");

    if (!$mw_basic[cf_kcb_type]) return true;
    if (!$mw_basic[cf_kcb_id]) return true;

    if (get_session("ss_okname")) return true;

    if ($member[mb_id]) {
        $sql = "select * from $mw[okname_table] where mb_id = '$member[mb_id]'";
        $row = sql_fetch($sql, false);
        if ($row) {
            set_session("ss_okname", $row[ok_name]);
            return true;
        }
    }
    return false;
}

function check_okname()
{
    global $mw_basic, $g4, $member, $board_skin_path, $bo_table, $board;

    if (!$mw_basic[cf_kcb_id]) return false;

    echo "<link rel='stylesheet' href='$board_skin_path/style.common.css' type='text/css'>\n";
    echo "<style type='text/css'> #mw_basic { display:none; } </style>\n";

    $req_file = null;

    if ($mw_basic[cf_kcb_type] == "19ban")
        $req_file = "$board_skin_path/mw.proc/mw.19ban.php"; // 19금
    else
        $req_file = "$board_skin_path/mw.proc/mw.okname.php"; // 실명인증

    if (file_exists($req_file)) require($req_file);
}

// 자동치환
function mw_reg_str($str)
{
    global $member;

    if ($member[mb_id]) {
        $str = str_replace("{닉네임}", $member[mb_nick], $str);
        $str = str_replace("{별명}", $member[mb_nick], $str);
    } else {
        $str = str_replace("{닉네임}", "회원", $str);
        $str = str_replace("{별명}", "회원", $str);
    }

    return $str;
}

function mw_write_file($file, $contents)
{
    $fp = fopen($file, "w");
    ob_start();
    print_r($contents);
    $msg = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $msg);
    fclose($fp);
}

function mw_read_file($file)
{
    ob_start();
    @readfile($file);
    $contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}

function mw_basic_read_config_file()
{
    global $g4, $mw_basic, $mw_basic_config_file;

    $contents = mw_read_file($mw_basic_config_file);
    $contents = base64_decode($contents);
    $contents = unserialize($contents);

    return $contents;
}

function mw_basic_write_config_file()
{
    global $g4, $mw, $bo_table, $mw_basic_config_file, $mw_basic_config_path;

    $sql = "select * from $mw[basic_config_table] where bo_table = '$bo_table'";
    $mw_basic = sql_fetch($sql, false);

    $contents = $mw_basic;
    $contents = serialize($contents);
    $contents = base64_encode($contents);

    $f = fopen($mw_basic_config_file, "w");
    fwrite($f, $contents);
    fclose($f);
    @chmod($mw_basic_config_file, 0600);

    if (!file_exists("$mw_basic_config_path/.htaccess")) {
        $f = fopen("$mw_basic_config_path/.htaccess", "w");
        fwrite($f, "Deny from All");
        fclose($f);
    }
}

function mw_basic_sns_date($datetime)
{
    global $g4;

    $timestamp = strtotime($datetime); // 글쓴날짜시간 Unix timestamp 형식 
    $current = $g4['server_time']; // 현재날짜시간 Unix timestamp 형식 

    // 1년전 
    if ($timestamp <= $current - 86400 * 365)
        $str = (int)(($current - $timestamp) / (86400 * 365)) . "년전"; 
    else if ($timestamp <= $current - 86400 * 31)
        $str = (int)(($current - $timestamp) / (86400 * 31)) . "개월전"; 
    else if ($timestamp <= $current - 86400 * 1)
        $str = (int)(($current - $timestamp) / 86400) . "일전"; 
    else if ($timestamp <= $current - 3600 * 1)
        $str = (int)(($current - $timestamp) / 3600) . "시간전"; 
    else if ($timestamp <= $current - 60 * 1)
        $str = (int)(($current - $timestamp) / 60) . "분전"; 
    else
        $str = (int)($current - $timestamp) . "초전"; 
    
    return $str; 
}

function mw_basic_counting_date($datetime, $endstr=" 남았습니다")
{
    global $g4;

    $timestamp = strtotime($datetime); // 글쓴날짜시간 Unix timestamp 형식 
    $current = $g4['server_time']; // 현재날짜시간 Unix timestamp 형식 

    if ($current >= $timestamp)
        return "종료 되었습니다.";

    if ($current <= $timestamp - 86400 * 365)
        $str = (int)(($timestamp - $current) / (86400 * 365)) . "년"; 
    else if ($current <= $timestamp - 86400 * 31)
        $str = (int)(($timestamp - $current) / (86400 * 31)) . "개월"; 
    else if ($current <= $timestamp - 86400 * 1)
        $str = (int)(($timestamp - $current) / 86400) . "일"; 
    else if ($current <= $timestamp - 3600 * 1)
        $str = (int)(($timestamp - $current) / 3600) . "시간"; 
    else if ($current <= $timestamp - 60 * 1)
        $str = (int)(($timestamp - $current) / 60) . "분"; 
    else
        $str = (int)($timestamp - $current) . "초"; 
    
    return $str.$endstr; 
}

function bc_code($str, $is_content=1) {
    global $g4, $bo_table, $wr_id, $board_skin_path;

    if ($is_content) {
        $str = preg_replace("/\[s\](.*)\[\/s\]/iU", "<s>$1</s>", $str);
        $str = preg_replace("/\[b\](.*)\[\/b\]/iU", "<b>$1</b>", $str);
        $str = preg_replace("/\[(h[1-9])\](.*)\[\/h[1-9]\]/iU", "<$1>$2</$1>", $str);
        $str = preg_replace("/\[file([0-9])\](.*)\[\/file[0-9]\]/iU", "<img src=\"$board_skin_path/img/icon_file_down.gif\" align=absmiddle> <span style='cursor:pointer; text-decoration:underline;' onclick=\"file_download('$g4[bbs_path]/download.php?bo_table=$bo_table&wr_id=$wr_id&no=$1', '', '$1');\">$2</span>", $str);
        $str = preg_replace("/\[red\](.*)\[\/red\]/iU", "<span style='color:#ff0000;'>$1</span>", $str);
    }

    $str = preg_replace("/\[month\]/iU", date('n', $g4[server_time]), $str);
    $str = preg_replace("/\[last_day\]/iU", date('t', $g4[server_time]), $str);

    preg_match_all("/\[counting (.*)\]/iU", $str, $matches);
    for ($i=0, $m=count($matches[1]); $i<$m; $i++) {
        $str = preg_replace("/\[counting {$matches[1][$i]}\]/iU", mw_basic_counting_date($matches[1][$i]), $str);
    }
    return $str;
}

function mw_spelling($str)
{
    global $g4, $board_skin_path;

    return $str;

    $path = "$board_skin_path/mw.lib/mw.spelling";
    if (file_exists($path)) {
        $tmp = mw_read_file($path);
        $list = explode(",", $tmp);
        for ($i=0, $m=count($list); $i<$m; $i++) {
            $spell = trim($list[$i]);
            if (!$spell) continue;
            $spell = explode("-", $spell);
            $str = preg_replace("/{$spell[0]}/", $spell[1], $str);
        }
    }

    if (strtolower(preg_replace('/-/', '', $g4[charset])) == 'euckr') {
        $str = convert_charset("euckr", "cp949//IGNORE", $str);
    }

    return $str;
}

function mw_get_ccl_info($ccl)
{
    $info = array();

    switch ($ccl)
    {
        case "by":
            $info[by] = "by";
            $info[nc] = "";
            $info[nd] = "";
            $info[kr] = "저작자표시";
            break;
        case "by-nc":
            $info[by] = "by";
            $info[nc] = "nc";
            $info[nd] = "";
            $info[kr] = "저작자표시-비영리";
            break;
        case "by-sa":
            $info[by] = "by";
            $info[nc] = "";
            $info[nd] = "sa";
            $info[kr] = "저작자표시-동일조건변경허락";
            break;
        case "by-nd":
            $info[by] = "by";
            $info[nc] = "";
            $info[nd] = "nd";
            $info[kr] = "저작자표시-변경금지";
            break;
        case "by-nc-nd":
            $info[by] = "by";
            $info[nc] = "nc";
            $info[nd] = "nd";
            $info[kr] = "저작자표시-비영리-변경금지";
            break;
        case "by-nc-sa":
            $info[by] = "by";
            $info[nc] = "nc";
            $info[nd] = "sa";
            $info[kr] = "저작자표시-비영리-동일조건변경허락";
            break;
        default :
            $info[by] = "";
            $info[nc] = "nc";
            $info[nd] = "nd";
            $info[kr] = "";
            break;
    }
    $info[ccl] = $ccl;
    $info[msg] = "크리에이티브 커먼즈 코리아 $info[kr] 2.0 대한민국 라이센스에 따라 이용하실 수 있습니다.";
    $info[link] = "http://creativecommons.org/licenses/{$ccl}/2.0/kr/";
    
    return $info;
}

function mw_delete_row($board, $write, $save_log=false, $save_message='삭제되었습니다.')
{
    global $g4, $member, $is_admin;

    $write_table = "$g4[write_prefix]$board[bo_table]";

    $row = sql_fetch("select * from $write_table where wr_id = '$write[wr_id]'");
    if (!$row)
        return;

    $board_skin_path = "$g4[path]/skin/board/$board[bo_skin]";
    $lib_file_path = "$board_skin_path/mw.lib/mw.skin.basic.lib.php";
    if (file_exists($lib_file_path)) include($lib_file_path);

    $count_write = 0;
    $count_comment = 0;

    // 썸네일 삭제
    if ($thumb_path) {
        $thumb_file = "$thumb_path/$write[wr_id]";
        if (file_exists($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb2_path) {
        $thumb_file = "$thumb2_path/$write[wr_id]";
        if (file_exists($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb3_path) {
        $thumb_file = "$thumb3_path/$write[wr_id]";
        if (file_exists($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb4_path) {
        $thumb_file = "$thumb4_path/$write[wr_id]";
        if (file_exists($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb5_path) {
        $thumb_file = "$thumb5_path/$write[wr_id]";
        if (file_exists($thumb_file)) @unlink($thumb_file);
    }

    // 워터마크 삭제
    if ($watermark_path) {
        $sql = " select * from $g4[board_file_table] ";
        $sql.= " where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' and bf_width > 0  order by bf_no";
        $qry = sql_query($sql);
        while ($row = sql_fetch_array($qry)) {
            @unlink("$watermark_path/$row[bf_file]");
        }

        // 에디터 이미지 및 워터마크 삭제
        mw_delete_editor_image($write[wr_content]);
    }

    // 팝업공지 삭제
    sql_query("delete from $mw[popup_notice_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ", false);

    // 코멘트 삭제
    if ($write[wr_is_comment]) {

        // 코멘트 추천 삭제 
        if ($mw[comment_good_table]) 
            sql_query("delete from $mw[comment_good_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'");

        // 코멘트 포인트 삭제
        if (!delete_point($write[mb_id], $board[bo_table], $write[wr_id], '코멘트'))
            insert_point($write[mb_id], $board[bo_comment_point] * (-1), "$board[bo_subject] {$write[wr_parent]}-{$write[wr_id]} 코멘트삭제");

        // 업로드된 파일이 있다면 파일삭제
        if ($mw[comment_file_table]) {
            $sql = " select * from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ";
            $qry = sql_query($sql);
            while ($row = sql_fetch_array($qry))
                @unlink("$g4[path]/data/file/$board[bo_table]/$row[bf_file]");

            // 파일테이블 행 삭제
            sql_query(" delete from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");
        }

        // 럭키라이팅 삭제
        if (function_exists("mw_delete_lucky_writing")) mw_delete_lucky_writing($board, $write);

        $count_comment++;
    }
    // 원글삭제
    else { 
        $sql = " select wr_id, mb_id, wr_is_comment from $write_table where wr_parent = '$write[wr_id]' order by wr_id ";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) 
        {
            // 원글이라면
            if (!$row[wr_is_comment]) 
            {
                // 원글 포인트 삭제
                if (!delete_point($row[mb_id], $board[bo_table], $row[wr_id], '쓰기'))
                    insert_point($row[mb_id], $board[bo_write_point] * (-1), "$board[bo_subject] $row[wr_id] 글삭제");

                // qna 포인트 삭제
                delete_point($row[mb_id], $board[bo_table], $row[wr_id], '@qna');
                delete_point($row[mb_id], $board[bo_table], $row[wr_id], '@qna-hold');
                delete_point($row[mb_id], $board[bo_table], $row[wr_id], '@qna-choose');

                // 럭키라이팅 삭제
                if (function_exists("mw_delete_lucky_writing")) mw_delete_lucky_writing($board, $row);

                // 업로드된 파일이 있다면 파일삭제
                $sql2 = " select * from $g4[board_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ";
                $result2 = sql_query($sql2);
                while ($row2 = sql_fetch_array($result2))
                    @unlink("$g4[path]/data/file/$board[bo_table]/$row2[bf_file]");
                    
                // 파일테이블 행 삭제
                sql_query(" delete from $g4[board_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ");

                // 추천
                sql_query(" delete from $g4[board_good_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

                $count_write++;
            } 
            // 코멘트라면
            else 
            {
                // 업로드된 파일이 있다면 파일삭제
                if ($mw[comment_file_table]) {
                    $sql2 = " select * from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ";
                    $qry2 = sql_query($sql2);
                    while ($row2 = sql_fetch_array($qry2))
                        @unlink("$g4[path]/data/file/$board[bo_table]/$row2[bf_file]");
                        
                    // 파일테이블 행 삭제
                    sql_query(" delete from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ");
                }

                // 코멘트 추천
                if ($mw[comment_good_table])
                    sql_query(" delete from $mw[comment_good_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

                // 코멘트 포인트 삭제
                if (!delete_point($row[mb_id], $board[bo_table], $row[wr_id], '코멘트'))
                    insert_point($row[mb_id], $board[bo_comment_point] * (-1), "$board[bo_subject] {$write[wr_id]}-{$row[wr_id]} 코멘트삭제");

                // 럭키라이팅 삭제
                if (function_exists("mw_delete_lucky_writing")) mw_delete_lucky_writing($board, $row);

                $count_comment++;
            }
        }
    }

    // 게시글 삭제
    if ($save_log != 'no' && ($mw_basic[cf_delete_log] || $save_log)) {
        if ($mw_basic[cf_post_history]) {
            //$wr_name2 = $board[bo_use_name] ? $member[mb_name] : $member[mb_nick];
            $sql = "insert into $mw[post_history_table]
                       set bo_table = '$board[bo_table]'
                           ,wr_id = '$write[wr_id]'
                           ,wr_parent = '$write[wr_parent]'
                           ,mb_id = '$write[mb_id]'
                           ,ph_name = '$write[wr_name]'
                           ,ph_option = '$write[wr_option]'
                           ,ph_subject = '".addslashes($write[wr_subject])."'
                           ,ph_content = '".addslashes($write[wr_content])."'
                           ,ph_ip = '$_SERVER[REMOTE_ADDR]'
                           ,ph_datetime = '$g4[time_ymdhis]'";
            sql_query($sql);
        }
        $sql = " update $write_table
                    set wr_subject = '$save_message'
                        ,wr_content = '$save_message'
                        ,wr_option = ''
                        ,wr_link1 = ''
                        ,wr_link2 = ''
                  where wr_id = '$write[wr_id]'";
        sql_query($sql);
    } else {
        // 원글삭제
        sql_query(" delete from $write_table where wr_parent = '$write[wr_id]' ");
        sql_query(" delete from $write_table where wr_id = '$write[wr_id]' ");

        // 리워드
        sql_query("delete from $mw[reward_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[reward_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);

        // 설문
        $sql = "select vt_id from $mw[vote_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'";
        $row = sql_fetch($sql, false);
        sql_query("delete from $mw[vote_item_table] where vt_id = '$row[vt_id]'", false);
        sql_query("delete from $mw[vote_log_table] where vt_id = '$row[vt_id]'", false);
        sql_query("delete from $mw[vote_table] where vt_id = '$row[vt_id]'", false);

        // 기타
        sql_query("delete from $mw[download_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[link_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[post_history_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[singo_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[must_notice_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);

        // 최근게시물 삭제
        sql_query(" delete from $g4[board_new_table] where bo_table = '$board[bo_table]' and wr_parent = '$write[wr_id]' ");
        sql_query(" delete from $g4[board_new_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

        // 스크랩 삭제
        sql_query(" delete from $g4[scrap_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

        // 퀴즈삭제
        if ($mw_basic[cf_quiz] && file_exists("$quiz_path/_config.php")) {
            include("$quiz_path/_config.php");
            $row = sql_fetch(" select * from $mw_quiz[quiz_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");
            sql_query(" delete from $mw_quiz[quiz_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");
            sql_query(" delete from $mw_quiz[log_table] where qz_id = '$row[qz_id]' ");
        }

        // 소셜커머스 삭제
        if (file_exists("$social_commerce_path/delete.skin.php")) include("$social_commerce_path/delete.skin.php");

        // 재능마켓 삭제
        if (file_exists("$talent_market_path/delete.skin.php")) include("$talent_market_path/delete.skin.php");

        // 모아보기 삭제
        if (function_exists('mw_moa_delete')) mw_moa_delete($write[wr_id]);

        if ($write[wr_is_comment]) {
            // 원글의 코멘트 숫자를 감소(다시계산)
            $tmp = sql_fetch("select count(*) as cnt from $write_table where wr_parent = '$write[wr_parent]' and wr_is_comment = '1'");
            sql_query(" update $write_table set wr_comment = '$tmp[cnt]' where wr_id = '$write[wr_parent]' ");
        }
        // 글숫자 감소
        if ($count_write > 0 || $count_comment > 0) {
            sql_query(" update $g4[board_table] set bo_count_write = bo_count_write - '$count_write', bo_count_comment = bo_count_comment - '$count_comment' where bo_table = '$board[bo_table]' ");
        }
    }

    // 공지사항 삭제
    $notice_array = explode("\n", trim($board[bo_notice]));
    $bo_notice = "";
    for ($k=0; $k<count($notice_array); $k++)
        if ((int)$write[wr_id] != (int)$notice_array[$k])
            $bo_notice .= $notice_array[$k] . "\n";
    $bo_notice = trim($bo_notice);
    sql_query(" update $g4[board_table] set bo_notice = '$bo_notice' where bo_table = '$board[bo_table]' ");
}

function mw_anonymous_nick($mb_id, $wr_ip='')
{
    global $mw_anonymous_list, $mw_anonymous_index, $write;

    if (!$mb_id)
        $mb_id = $wr_ip;

    if (!$mw_anonymous_list[$mb_id])
    {
        if (!$mw_anonymous_index)
            $mw_anonymous_index = 1;

        if ($write[mb_id] == $mb_id || $write[wr_ip] == $wr_ip) {
            $mw_anonymous_list[$mb_id] = "익명글쓴이";
        } else {
            $mw_anonymous_list[$mb_id] = "익명{$mw_anonymous_index}호";
            $mw_anonymous_index++;
        }
    }
    return $mw_anonymous_list[$mb_id];
}

function mw_auto_bomb()
{
    global $g4, $mw_basic, $mw, $bo_table, $board;

    $sql = " select * from $mw[bomb_table] where bo_table = '$bo_table' and bm_datetime <= '$g4[time_ymdhis]' ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $write = sql_fetch("select * from $g4[write_prefix]$bo_table where wr_id = '$row[wr_id]'");
        mw_delete_row($board, $write);
        sql_query("delete from $mw[bomb_table] where bo_table = '$bo_table' and wr_id = '$row[wr_id]'");
    }
}

// 19+ : 19세 이상
// 19- : 19세 미만 
// 19= : 19세만 
function mw_basic_age($value)
{
    global $g4, $member;

    if (!$member[mb_birth])
        $member_age = 0;
    else
        $member_age = floor((date("Ymd", $g4[server_time]) - $member[mb_birth]) / 10000);

    preg_match("/^([0-9]+)([\+\-\=])$/", $value, $match);
    $age = $match[1];
    $age_type = $match[2];

    switch ($age_type) {
        case "+" :
            if ($member_age < $age) alert("나이 {$age}세 이상만 접근 가능합니다.");
            break;
        case "-" :
            if ($member_age >= $age) alert("나이 {$age}세 미만만 접근 가능합니다.");
            break;
        case "=" :
            if ($member_age != $age) alert("나이 {$age}세만 접근 가능합니다.");
            break;
    }
}

function mw_basic_move_cate($bo_table, $wr_id)
{
    global $g4, $mw_basic, $mw, $board, $write_table;

    $sql = " select * from $mw[move_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and mv_datetime <= '$g4[time_ymdhis]' ";
    $row = sql_fetch($sql);

    if (!$row) return;

    $notice_array = explode("\n", trim($board[bo_notice]));
    if ($row[mv_notice] == "u") {
        
        if (!in_array((int)$wr_id, $notice_array))
        {
            $bo_notice = $wr_id . '\n' . $board[bo_notice];
            sql_query(" update $g4[board_table] set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");
        }
    }
    else if ($row[mv_notice] == "d") {
        $bo_notice = '';
        for ($i=0; $i<count($notice_array); $i++)
            if ((int)$wr_id != (int)$notice_array[$i])
                $bo_notice .= $notice_array[$i] . '\n';
        $bo_notice = trim($bo_notice);
        sql_query(" update $g4[board_table] set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");

    }

    if ($row[mv_cate]) 
        sql_query( " update $write_table set ca_name = '$row[mv_cate]' where wr_id = '$wr_id' ");

    sql_query(" delete from $mw[move_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and mv_datetime <= '$g4[time_ymdhis]' ");
}

function mw_view_image($view, $number, $attribute)
{
    $ret = '';
    if ($view['file'][$number]['view']) {
        $ret = preg_replace("/>$/", " $attribute>", $view['file'][$number]['view']);
        if (trim($view['file'][$number][content]))
            $ret .= "<br/><br/>" . $view['file'][$number][content] . "<br/><br/>";
    }
    else {
        $ret = "{".$number."번 이미지 없음}";
    }
    return $ret;
}

function mw_move($wr_id_list, $chk_bo_table, $sw)
{
    global $g4, $bo_table, $write_table, $board, $member, $board_skin_path, $config, $is_admin;

    require("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

    if ($chk_bo_table && !is_array($chk_bo_table)) {
        $tmp = $chk_bo_table;
        $chk_bo_table = array();
        $chk_bo_table[] = $tmp;
    }

    $save = array();
    $save_count_write = 0;
    $save_count_comment = 0;
    $cnt = 0;

    // SQL Injection 으로 인한 코드 보완
    //$sql = " select distinct wr_num from $write_table where wr_id in (" . stripslashes($wr_id_list) . ") order by wr_id ";
    $sql = " select distinct wr_num from $write_table where wr_id in ($wr_id_list) order by wr_id ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) 
    {
        $wr_num = $row[wr_num];
        for ($i=0; $i<count($chk_bo_table); $i++) 
        {
            $move_bo_table = $chk_bo_table[$i];
            $move_write_table = $g4['write_prefix'] . $move_bo_table;

            $src_dir = "$g4[path]/data/file/$bo_table"; // 원본 디렉토리
            $dst_dir = "$g4[path]/data/file/$move_bo_table"; // 복사본 디렉토리

            $count_write = 0;
            $count_comment = 0;

            $next_wr_num = get_next_num($move_write_table);

            //$sql2 = " select * from $write_table where wr_num = '$wr_num' order by wr_parent, wr_comment desc, wr_id ";
            $sql2 = " select * from $write_table where wr_num = '$wr_num' order by wr_parent, wr_is_comment, wr_comment desc, wr_id ";
            $result2 = sql_query($sql2);
            while ($row2 = sql_fetch_array($result2)) 
            {
                $nick = cut_str($member[mb_nick], $config[cf_cut_name]);
                if (!$row2[wr_is_comment] && $config[cf_use_copy_log]) {
                    $row2[wr_content] .= "\n\n[이 게시물은 {$nick}님에 의해 $g4[time_ymdhis] {$board[bo_subject]}에서 " . ($sw == 'copy' ? '복사' : '이동') ." 됨]";
                    if ($sw == 'copy')
                        $row2[wr_content] .= "\n\n".set_http($g4[url])."/$g4[bbs]/board.php?bo_table=$board[bo_table]&wr_id=$row2[wr_id]";
                }

                $sql = " insert into $move_write_table
                            set wr_num            = '$next_wr_num',
                                wr_reply          = '$row2[wr_reply]',
                                wr_is_comment     = '$row2[wr_is_comment]',
                                wr_comment        = '$row2[wr_comment]',
                                wr_comment_reply  = '$row2[wr_comment_reply]',
                                ca_name           = '".addslashes($row2[ca_name])."',
                                wr_option         = '$row2[wr_option]',
                                wr_subject        = '".addslashes($row2[wr_subject])."',
                                wr_content        = '".addslashes($row2[wr_content])."',
                                wr_link1          = '".addslashes($row2[wr_link1])."',
                                wr_link2          = '".addslashes($row2[wr_link2])."',
                                wr_link1_hit      = '$row2[wr_link1_hit]',
                                wr_link2_hit      = '$row2[wr_link2_hit]',
                                wr_trackback      = '".addslashes($row2[wr_trackback])."',
                                wr_hit            = '$row2[wr_hit]',
                                wr_good           = '$row2[wr_good]',
                                wr_nogood         = '$row2[wr_nogood]',
                                mb_id             = '$row2[mb_id]',
                                wr_password       = '$row2[wr_password]',
                                wr_name           = '".addslashes($row2[wr_name])."',
                                wr_email          = '".addslashes($row2[wr_email])."',
                                wr_homepage       = '".addslashes($row2[wr_homepage])."',
                                wr_datetime       = '$row2[wr_datetime]',
                                wr_last           = '$row2[wr_last]',
                                wr_ip             = '$row2[wr_ip]',
                                wr_1              = '".addslashes($row2[wr_1])."',
                                wr_2              = '".addslashes($row2[wr_2])."',
                                wr_3              = '".addslashes($row2[wr_3])."',
                                wr_4              = '".addslashes($row2[wr_4])."',
                                wr_5              = '".addslashes($row2[wr_5])."',
                                wr_6              = '".addslashes($row2[wr_6])."',
                                wr_7              = '".addslashes($row2[wr_7])."',
                                wr_8              = '".addslashes($row2[wr_8])."',
                                wr_9              = '".addslashes($row2[wr_9])."',
                                wr_10             = '".addslashes($row2[wr_10])."' ";
                sql_query($sql);

                $insert_id = mysql_insert_id();

                if (!$row2[wr_is_comment]) { // 원글
                    $save_parent = $insert_id;
                    $count_write++;
                } else { // 코멘트
                    $count_comment++;
                }

                sql_query(" update $move_write_table set wr_parent = '$save_parent' where wr_id = '$insert_id' ");

                // 배추스킨 확장 필드 복사/이동
                // 필드별로 업데이트 하는 이유 : 버전업 과정에서 누락된 필드 오류를 그냥 넘어가기 위해
                sql_query(" update $move_write_table set wr_ccl = '$row2[wr_ccl]' where wr_id = '$insert_id' ");
                sql_query(" update $move_write_table set wr_singo = '$row2[wr_singo]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_zzal = '$row2[wr_zzal]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_related = '$row2[wr_related]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_comment_ban = '$row2[wr_comment_ban]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_contents_price = '$row2[wr_contents_price]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_contents_domain = '$row2[wr_contents_domain]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_umz = '' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_subject_font = '$row2[wr_subject_font]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_subject_color = '$row2[wr_subject_color]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_anonymous = '$row2[wr_anonymous]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_comment_hide = '$row2[wr_comment_hide]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_no_img_ext = '$row2[wr_no_img_ext]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_read_level = '$row2[wr_read_level]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_kcb_use = '$row2[wr_kcb_use]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_qna_status = '$row2[wr_qna_status]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_qna_point = '$row2[wr_qna_point]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_qna_id = '$row2[wr_qna_id]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_is_mobile = '$row2[wr_is_mobile]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_google_map = '$row2[wr_google_map]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_link_write = '$row2[wr_link_write]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_view_block = '$row2[wr_view_block]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_auto_move = '$row2[wr_auto_move]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_link1_target = '$row2[wr_link1_target]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_link2_target = '$row2[wr_link2_target]' where wr_id = '$insert_id' ", false);
                sql_query(" update $move_write_table set wr_contents_preview = '".addslashes($row2[wr_contents_preview])."' where wr_id = '$insert_id' ", false);

                // 첨부파일 복사
                $sql3 = " select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by bf_no ";
                $result3 = sql_query($sql3);
                for ($k=0; $row3 = sql_fetch_array($result3); $k++) 
                {
                    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
                    $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $row3[bf_source]);
                    shuffle($chars_array);
                    $shuffle = implode("", $chars_array);
                    $filename = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename))); 

                    if ($row3[bf_file]) { // 원본파일을 복사하고 퍼미션을 변경
                        @copy("$src_dir/$row3[bf_file]", "$dst_dir/$filename");
                        @chmod("$dst_dir/$filename", 0606);
                    }

                    $sql = " insert into $g4[board_file_table] 
                                set bo_table = '$move_bo_table', 
                                    wr_id = '$insert_id', 
                                    bf_no = '$row3[bf_no]', 
                                    bf_source = '".addslashes($row3[bf_source])."', 
                                    bf_file = '$filename', 
                                    bf_download = '$row3[bf_download]', 
                                    bf_content = '".addslashes($row3[bf_content])."',
                                    bf_filesize = '$row3[bf_filesize]',
                                    bf_width = '$row3[bf_width]',
                                    bf_height = '$row3[bf_height]',
                                    bf_type = '$row3[bf_type]',
                                    bf_datetime = '$row3[bf_datetime]' ";
                    sql_query($sql);

                    if ($sw == "move" && $row3[bf_file])
                        $save[$cnt][bf_file][$k] = "$src_dir/$row3[bf_file]";
                }

                // 코멘트 첨부파일 복사
                $sql3 = " select * from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by bf_no ";
                $result3 = sql_query($sql3);
                for ($k=0; $row3 = sql_fetch_array($result3); $k++) 
                {
                    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
                    $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $row3[bf_source]);
                    shuffle($chars_array);
                    $shuffle = implode("", $chars_array);
                    $filename = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename))); 

                    if ($row3[bf_file]) { // 원본파일을 복사하고 퍼미션을 변경
                        @copy("$src_dir/$row3[bf_file]", "$dst_dir/$filename");
                        @chmod("$dst_dir/$filename", 0606);
                    }

                    $sql = " insert into $mw[comment_file_table] 
                                set bo_table = '$move_bo_table', 
                                    wr_id = '$insert_id', 
                                    bf_no = '$row3[bf_no]', 
                                    bf_source = '".addslashes($row3[bf_source])."', 
                                    bf_file = '$filename', 
                                    bf_download = '$row3[bf_download]', 
                                    bf_content = '".addslashes($row3[bf_content])."',
                                    bf_filesize = '$row3[bf_filesize]',
                                    bf_width = '$row3[bf_width]',
                                    bf_height = '$row3[bf_height]',
                                    bf_type = '$row3[bf_type]',
                                    bf_datetime = '$row3[bf_datetime]' ";
                    sql_query($sql);

                    if ($sw == "move" && $row3[bf_file])
                        $save[$cnt][bf_file][$k] = "$src_dir/$row3[bf_file]";
                }

                //////////////////////////////////////////////////////////////////////////////
                // 복사 스크립트
                //////////////////////////////////////////////////////////////////////////////
                if ($sw == "copy")
                {
                    // 최신글 등록
                    $sql = " insert into $g4[board_new_table] ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) ";
                    $sql.= " values ( '$move_bo_table', '$insert_id', '$save_parent', '$row2[wr_datetime]', '$row2[mb_id]' ) ";
                    sql_query($sql);

                    // 리워드
                    $tmp = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'");
                    if ($tmp) {
                        $sql_common = "bo_table     = '$move_bo_table'";
                        $sql_common.= ", wr_id      = '$insert_id'";
                        $sql_common.= ", re_site    = '".addslashes($tmp[re_site])."'";
                        $sql_common.= ", re_point   = '$tmp[re_point]'";
                        $sql_common.= ", re_url     = '".addslashes($tmp[re_url])."'";
                        $sql_common.= ", re_edate   = '$tmp[re_edate]'";
                        sql_query("insert into $mw[reward_table] set $sql_common, re_status = '1'");
                    }

                    // 설문
                    $tmp = sql_fetch("select * from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'");
                    if ($tmp) {
                        $vt_id = $tmp[vt_id];

                        $sql = "insert into $mw[vote_table] set bo_table = '$move_bo_table'";
                        $sql.= ", wr_id = '$insert_id' ";
                        $sql.= ", vt_edate = '$tmp[vt_edate]' ";
                        $sql.= ", vt_total = '$tmp[vt_total]' ";
                        $sql.= ", vt_point = '$tmp[vt_point]' ";
                        sql_query($sql);

                        $insert_vt_id = mysql_insert_id();

                        $qry = sql_query("select * from $mw[vote_item_table] where vt_id = '$vt_id' order by vt_num");
                        while ($tmp = sql_fetch_array($qry)) {
                            sql_query("insert into $mw[vote_item_table] set vt_id = '$insert_vt_id', vt_num = '$tmp[vt_num]', vt_item = '$tmp[vt_item]', vt_hit = '$tmp[vt_hit]'");
                        }

                        $qry = sql_query("select * from $mw[vote_log_table] where vt_id = '$tmp[vt_id]' order by vt_num");
                        while ($tmp = sql_fetch_array($qry)) {
                            sql_query("insert into $mw[vote_log_table] set vt_id = '$insert_vt_id', vt_num = '$tmp[vt_num]', mb_id = '$tmp[mb_id]', vt_ip = '$tmp[vt_ip]', vt_datetime = '$tmp[vt_datetime]'");
                        }
                    }

                    // 글 변경로그
                    $qry = sql_query("select * from $mw[post_history_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by ph_id", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", wr_parent      = '$save_parent'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", ph_name        = '$tmp[ph_name]'";
                        $sql_common.= ", ph_option      = '$tmp[ph_option]'";
                        $sql_common.= ", ph_subject     = '".addslashes($tmp[ph_subject])."'";
                        $sql_common.= ", ph_content     = '".addslashes($tmp[ph_content])."'";
                        $sql_common.= ", ph_ip          = '$tmp[ph_ip]'";
                        $sql_common.= ", ph_datetime    = '$tmp[ph_datetime]'";
                        sql_query("insert into $mw[post_history_table] set $sql_common");
                    }

                    // 다운로드 로그
                    $qry = sql_query("select * from $mw[download_log_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by dl_id", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", bf_no          = '$tmp[bf_no]'";
                        $sql_common.= ", dl_name        = '$tmp[dl_name]'";
                        $sql_common.= ", dl_ip          = '$tmp[dl_ip]'";
                        $sql_common.= ", dl_datetime    = '$tmp[dl_datetime]'";
                        sql_query("insert into $mw[download_log_table] set $sql_common");
                    }

                    // 원글추천
                    $qry = sql_query("select * from $g4[board_good_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by bg_id ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", bg_flag        = '$tmp[bg_flag]'";
                        $sql_common.= ", bg_datetime    = '$tmp[bg_datetime]'";
                        sql_query("insert into $g4[board_good_table] set $sql_common");                   
                    }

                    // 코멘트추천
                    $qry = sql_query("select * from $mw[comment_good_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", parent_id      = '$save_parent'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", bg_flag        = '$tmp[bg_flag]'";
                        $sql_common.= ", bg_datetime    = '$tmp[bg_datetime]'";
                        sql_query("insert into $mw[comment_good_table] set $sql_common");                   
                    }

                    // 신고로그
                    $qry = sql_query("select * from $mw[singo_log_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by si_id ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", si_type        = '$tmp[si_type]'";
                        $sql_common.= ", si_memo        = '$tmp[si_memo]'";
                        $sql_common.= ", si_ip          = '$tmp[si_ip]'";
                        $sql_common.= ", si_datetime    = '$tmp[si_datetime]'";
                        sql_query("insert into $mw[singo_log_table] set $sql_common");                   
                    }

                    // 링크로그
                    $qry = sql_query("select * from $mw[link_log_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by ll_id ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", ll_no          = '$tmp[ll_lo]'";
                        $sql_common.= ", ll_name        = '$tmp[ll_name]'";
                        $sql_common.= ", ll_ip          = '$tmp[ll_ip]'";
                        $sql_common.= ", ll_datetime    = '$tmp[ll_datetime]'";
                        sql_query("insert into $mw[link_log_table] set $sql_common");                   
                    }

                    // 공지필수
                    $qry = sql_query("select * from $mw[must_notice_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", mu_datetime    = '$tmp[mu_datetime]'";
                        sql_query("insert into $mw[must_notice_table] set $sql_common");                   
                    }
                }

                //////////////////////////////////////////////////////////////////////////////
                // 이동 스크립트
                //////////////////////////////////////////////////////////////////////////////
                else if ($sw == "move")
                {
                    $save[$cnt][wr_id] = $row2[wr_parent];

                    // 썸네일 삭제
                    @unlink("$thumb_path/$row2[wr_id]");
                    @unlink("$thumb2_path/$row2[wr_id]");
                    @unlink("$thumb3_path/$row2[wr_id]");
                    @unlink("$thumb4_path/$row2[wr_id]");
                    @unlink("$thumb5_path/$row2[wr_id]");

                    // 워터마크 삭제
                    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' and bf_width > 0  order by bf_no";
                    $qry = sql_query($sql);
                    while ($file = sql_fetch_array($qry)) {
                        @unlink("$watermark_path/$row[bf_file]");
                    }

                    // 스크랩 이동
                    $sql = " update $g4[scrap_table] set bo_table = '$move_bo_table', wr_id = '$save_parent' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ";
                    sql_query($sql);

                    // 최신글 이동
                    $sql = " update $g4[board_new_table] set bo_table = '$move_bo_table', wr_id = '$insert_id', wr_parent = '$save_parent' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ";
                    sql_query($sql);

                    // 리워드
                    $sql = " update $mw[reward_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    $sql = " update from $mw[reward_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 설문
                    $sql = " update $mw[vote_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 글 변경로그
                    $sql = " update $mw[post_history_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 다운로드 로그
                    $sql = " update $mw[download_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 원글 추천
                    $sql = " update $g4[board_good_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 코멘트 추천
                    $sql = " update $mw[comment_good_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 코멘트 추천
                    $sql = " update $mw[comment_good_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 신고로그
                    $sql = " update $mw[singo_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 링크로그
                    $sql = " update $mw[link_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 공지필수
                    $sql = " update $mw[must_notice_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 모아보기 삭제
                    if (function_exists('mw_moa_delete')) mw_moa_delete($row2[wr_id]);

                    // 팝업공지 삭제
                    sql_query("delete from $mw[popup_notice_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ", false);
                }

                // 소셜커머스
                if (!$row2[wr_is_comment] && file_exists("$social_commerce_path/move_update.skin.php")) {
                    include("$social_commerce_path/move_update.skin.php");
                }

                // 재능마켓
                if (!$row2[wr_is_comment] && file_exists("$talent_market_path/move_update.skin.php")) {
                    include("$talent_market_path/move_update.skin.php");
                }

                // 퀴즈
                if (!$row2[wr_is_comment] && file_exists("$quiz_path/move_update.skin.php")) {
                    include("$quiz_path/_config.php");
                    include("$quiz_path/move_update.skin.php");
                }

                $cnt++;
            }

            sql_query(" update $g4[board_table] set bo_count_write   = bo_count_write   + '$count_write'   where bo_table = '$move_bo_table' ");
            sql_query(" update $g4[board_table] set bo_count_comment = bo_count_comment + '$count_comment' where bo_table = '$move_bo_table' ");
        }

        $save_count_write += $count_write;
        $save_count_comment += $count_comment;
    }

    if ($sw == "move") 
    {
        for ($i=0; $i<count($save); $i++) 
        {
            //  파일삭제
            for ($k=0; $k<count($save[$i][bf_file]); $k++) {
                @unlink($save[$i][bf_file][$k]);    
            }

            sql_query(" delete from $write_table where wr_parent = '{$save[$i][wr_id]}' "); // 원글삭제
            sql_query(" delete from $g4[board_new_table] where bo_table = '$bo_table' and wr_id = '{$save[$i][wr_id]}' "); // 최신글 삭제
            sql_query(" delete from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '{$save[$i][wr_id]}' "); // 파일정보 삭제
            sql_query(" delete from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '{$save[$i][wr_id]}' "); // 코멘트 파일정보 삭제
        }
        // 게시판 글수 카운터 조정
        $sql = " update $g4[board_table] set ";
        $sql.= "   bo_count_write = bo_count_write - '$save_count_write' ";
        $sql.= " , bo_count_comment = bo_count_comment - '$save_count_comment' ";
        $sql.= " where bo_table = '$bo_table' ";
        sql_query($sql);
    }

    // 공지사항에는 등록되어 있지만 실제 존재하지 않는 글 아이디는 삭제합니다.
    $bo_notice = "";
    $lf = "";
    if ($board[bo_notice]) {
        $tmp_array = explode("\n", $board[bo_notice]);
        for ($i=0; $i<count($tmp_array); $i++) {
            $tmp_wr_id = trim($tmp_array[$i]);
            $row = sql_fetch(" select count(*) as cnt from $g4[write_prefix]$bo_table where wr_id = '$tmp_wr_id' ");
            if ($row[cnt]) 
            {
                $bo_notice .= $lf . $tmp_wr_id;
                $lf = "\n";
            }
        }
    }
    $sql = " update $g4[board_table] set bo_notice = '$bo_notice' where bo_table = '$bo_table' ";
    sql_query($sql);
}

function mw_bomb()
{
    global $board, $g4, $mw, $mw_basic;

    $is_bomb = false;
    $sql = " select * from $mw[bomb_table] where bo_table = '$board[bo_table]' and bm_datetime <= '$g4[time_ymdhis]' ";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry)) {
        $tmp = sql_fetch("select * from $g4[write_prefix]$board[bo_table] where wr_id = '$row[wr_id]'");
        mw_delete_row($board, $tmp, $row[bm_log], '폭파되었습니다.');
        sql_query("delete from $mw[bomb_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]'", false);
        $is_bomb = true;
    }
    if ($is_bomb) {
        ?><script type="text/javascript">location.reload();</script><?
        exit;
    }
}

function mw_tag_debug($str) // 잘못된 태그교정
{
    $tags = array('td', 'tr', 'table', 'div', 'ol', 'ul', 'span');

    foreach ($tags as $tag) {
        $sc = preg_match_all("/<$tag/i", $str, $matchs);
        $ec = preg_match_all("/<\/$tag/i", $str, $matchs);

        if ($sc > $ec) $str.= str_repeat("</$tag>", $sc-$ec);
        if ($sc < $ec) $str = str_repeat("<$tag>", $ec-$sc).$str;
    }
    return $str;
}

