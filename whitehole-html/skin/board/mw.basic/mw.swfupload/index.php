<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$swfupload_path         = "$board_skin_path/mw.swfupload"; // swfupload 경로
$thumb_width            = "90"; // 썸네일 가로 사이즈
$thumb_height           = "60"; // 썸네일 세로 사이즈

$file_types             = "*.*"; // 업로드 가능 파일 확장자
$file_types_description = "All Files"; // 업로드 가능 확장자 설명
$file_allsize_limit     = "100 MB"; // 업로드 가능 총파일 용량
$file_size_limit        = getfilesize($board[bo_upload_size]); // 업로드 가능 파일 용량
$file_upload_limit      = "0"; // 업로드 가능 파일 수. 0은 무한대
$file_queue_limit       = "0"; // 큐에 들어갈 수 있는 파일 수. 0은 무한대

if ($wr_id) {
    $board_file_path = "$g4[path]/data/file/$bo_table"; // 파일폴더 경로
    $sql  = " select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ";
} else {
    $board_file_path  = "$g4[path]/data/guploader"; // 파일폴더 경로
    $sql = " select * from $mw[guploader_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and bf_ip = '$_SERVER[REMOTE_ADDR]' ";
}

$qry = sql_query($sql);
$sum_filesize = 0;
$last_bf_no = 0;
for ($i=0; $row=sql_fetch_array($qry); $i++) {
    if ($row[bf_file]) {
        $file_list_option .= "<option value='$row[bf_no]|$row[bf_source]|$row[bf_file]|$row[bf_filesize]|$row[bf_width]|$row[bf_type]'>$row[bf_source] (" . getfilesize($row[bf_filesize]) . ")</option>\n";
        $sum_filesize = $sum_filesize + $row[bf_filesize];
        $last_bf_no++;
    }
}

// 업로드 상태확인
$uploader_status = "문서첨부제한 : " . getfilesize($sum_filesize) . " / " . $file_allsize_limit;
$uploader_status.= "<br />파일제한크기 : " . getfilesize($board[bo_upload_size]) . " (허용확장자 : " . $file_types_description . ")";

// 파일 사이즈를 kb, mb에 맞추어서 변환해서 리턴 
function getfilesize($size) {
    if (!$size) return "0 Byte";
    if ($size < 1024) {
        return ($size." Byte");
    } else if ($size > 1024 && $size < 1024 *1024) {
        return sprintf("%0.1f KB",$size / 1024);
    }
    else return sprintf("%0.2f MB",$size / (1024*1024));
}
?>

<style type="text/css">
.swfupload { position: absolute; z-index: 1; }

span.button,
span.button input { position:relative; border:0; font:11px 굴림; background:url(<?=$board_skin_path?>/img/buttonWhite.gif) no-repeat; }
span.button input { width:60px; height:24px; left:1px; padding-top:2px; text-align:center; background-position:right top; cursor:pointer; overflow:visible; }
</style>

<script type="text/javascript" src="<?=$swfupload_path?>/swfupload.js"></script>
<script type="text/javascript" src="<?=$swfupload_path?>/swfupload.swfobject.js"></script>
<script type="text/javascript" src="<?=$swfupload_path?>/swfupload.queue.js"></script>
<script type="text/javascript" src="<?=$swfupload_path?>/fileprogress.js"></script>
<script type="text/javascript" src="<?=$swfupload_path?>/handlers.js"></script>
<script type="text/javascript">
var swfu;

// File Delete Settings
var board_skin_path        = "<?=$board_skin_path?>"
var swfupload_path         = "<?=$swfupload_path?>";
var bo_table               = "<?=$bo_table?>";
var mb_id                  = "<?=$member[mb_id]?>";
var wr_id                  = "<?=$wr_id?>";
var board_file_table       = "<?=$board_file_table?>";
var board_file_path        = "<?=$board_file_path?>";
var thumb_width            = "<?=$thumb_width?>";
var thumb_height           = "<?=$thumb_height?>";
var file_types             = "<?=$file_types?>";
var file_types_description = "<?=$file_types_description?>";
var file_allsize_limit     = "<?=$file_allsize_limit?>";
var file_size_limit        = "<?=$file_size_limit?>";
var sum_filesize           = <?=$sum_filesize?>;
var file_upload_limit      = <?=$file_upload_limit?>;
var file_queue_limit       = <?=$file_queue_limit?>;
var last_bf_no             = <?=$last_bf_no?>;
var last_bf_position       = 0;

SWFUpload.onload = function () {
    var settings = {
        // Flash Settings
        flash_url : "<?=$swfupload_path?>/swfupload.swf",

        // File Upload Settings
        upload_url : "<?=$swfupload_path?>/file_upload.php",
        post_params : {
            "bo_table"         : bo_table,
            "mb_id"            : mb_id,
            "wr_id"            : wr_id,
            "board_file_path"  : board_file_path
        },

        // File Upload Settings
        file_types             : file_types,
        file_types_description : file_types_description,
        file_size_limit        : file_size_limit,
        file_upload_limit      : file_upload_limit,
        file_queue_limit       : file_queue_limit,
        custom_settings        : {
            fileListAreaID     : "uploaded_file_list",
            cancelButtonId     : "btnCancel"
        },

        // Button settings
        button_placeholder_id : "spanButtonPlaceholder",
        button_width          : 60,
        button_height         : 24,
        button_window_mode    : SWFUpload.WINDOW_MODE.TRANSPARENT,
        button_cursor         : SWFUpload.CURSOR.HAND,

        // The event handler functions are defined in handlers.js
        swfupload_loaded_handler     : swfUploadLoaded,
        file_queued_handler          : fileQueued,
        file_queue_error_handler     : fileQueueError,
        file_dialog_complete_handler : fileDialogComplete,
        upload_start_handler         : uploadStart,
        upload_progress_handler      : uploadProgress,
        upload_error_handler         : uploadError,
        upload_success_handler       : uploadSuccess,
        upload_complete_handler      : uploadComplete,
        
        // SWFObject settings
        minimum_flash_version         : "9.0.28",
        swfupload_pre_load_handler    : swfUploadPreLoad,
        swfupload_load_failed_handler : swfUploadLoadFailed
    };

    swfu = new SWFUpload(settings);
};
</script>

<div id="swfup_content" style="padding:5px 0;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <col width=100 />
    <col width=5 />
    <col width=200 />
    <col width=5 /><col />
    <tr>
        <td id="image_preview" valign="center" align="center"
            style="background-color:#efefef; border:1px solid #ccc; text-align:center; font-size:11px;">미리보기</td>
        <td></td>
        <td><select name="uploaded_file_list" id="uploaded_file_list" style="width:200px; overflow:hidden;"
            size="5" onchange="preview();"><?=$file_list_option?></select></td>
        <td></td>
        <td valign="top">
            <div style="padding-bottom:10px;">
                <span id="spanButtonPlaceholder"></span>
                <span class="button"><input id="btnUpload" type="button" value="파일첨부" /></span>
                <span class="button"><input id="btnDelete" type="button" value="선택삭제" onClick="delete_file();" /></span>
                <span class="button"><input id="btnInsert" type="button" value="본문삽입" onClick="file_to_editor();" /></span>
            </div>
            <div>
                <span id="uploader_status" style="font-size:11px;"><?=$uploader_status?></span>
            </div>
        </td>
    </tr>
    </table>

    <div id="divSWFUploadUI" style="width:100%;">
        <noscript style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px;">
            We're sorry.  SWFUpload could not load.  You must have JavaScript enabled to enjoy SWFUpload.
        </noscript>
        <div id="divLoadingContent" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">
            SWFUpload is loading. Please wait a moment...
        </div>
        <div id="divLongLoading" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">
            SWFUpload is taking a long time to load or the load has failed.  Please make sure that the Flash Plugin is enabled and that a working version of the Adobe Flash Player is installed.
        </div>
        <div id="divAlternateContent" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">
            We're sorry.  SWFUpload could not load.  You may need to install or upgrade Flash Player.
            Visit the <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe website</a> to get the Flash Player.
        </div>
    </div>
</div>
