<?php
include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if ($wr_id)
    $board_file_path = "$g4[path]/data/file/$bo_table";
else
    $board_file_path = "$g4[path]/data/guploader";

// 디렉토리가 없다면 생성합니다. (퍼미션도 변경하구요.)
@mkdir("$board_file_path", 0707);
@chmod("$board_file_path", 0707);

$upload   = array();
$tmp_file = $_FILES['Filedata']['tmp_name'];
$filename = $_FILES['Filedata']['name'];
$filesize = $_FILES['Filedata']['size'];

if (!is_uploaded_file($tmp_file)) exit;

// 파일명 charset
if (strtolower(str_replace("-", "", $g4[charset])) == "euckr") {
    $tmp_name = @iconv("utf-8", "cp949", $filename);
    if (!$tmp_name)
        $tmp_name = @mb_convert_encoding($str, "cp949", "utf-8");  
    if (!$tmp_name)
        exit;
    $filename = $tmp_name;
}

$upload['source'] = $filename;
$upload['filesize'] = $filesize;
$timg = @getimagesize($tmp_file);

// 아래의 문자열이 들어간 파일은 -x 를 붙여서 웹경로를 알더라도 실행을 하지 못하도록 함
$filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)$/i", "$0-x", $filename);

// 접미사를 붙인 파일명
$upload['file'] = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr(md5(uniqid($g4['server_time'])), 0, 8).'_'.str_replace('%', '', urlencode($filename)); 

$dest_file = "$board_file_path/$upload[file]";

// 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
$error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['Filedata']['error']);

// 올라간 파일의 퍼미션을 변경합니다.
chmod($dest_file, 0606);

$bf_no = 0;

if ($wr_id) {
    $sql = "select max(bf_no) as bf_no from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id'";
    $row = sql_fetch($sql);
    if ($row[bf_no] >= 0)
        $bf_no = $row[bf_no] + 1;

    $sql = " insert into $g4[board_file_table]
                set bo_table = '$bo_table'
                    ,wr_id = '$wr_id'
                    ,bf_no = '$bf_no'
                    ,bf_source = '$upload[source]'
                    ,bf_file = '$upload[file]'
                    ,bf_filesize = '$upload[filesize]'
                    ,bf_width = '$timg[0]'
                    ,bf_height = '$timg[1]'
                    ,bf_type = '$timg[2]'
                    ,bf_datetime = '$g4[time_ymdhis]'";
    $qry = sql_query($sql);
} else {
    $sql = "select max(bf_no) as bf_no from $mw[guploader_table] where bo_table = '$bo_table' and mb_id = '$mb_id' and bf_ip = '$_SERVER[REMOTE_ADDR]' ";
    $row = sql_fetch($sql, false);
    if ($row[bf_no] >= 0)
        $bf_no = $row[bf_no] + 1;

    $sql = " insert into $mw[guploader_table]
                set bo_table = '$bo_table'
                    ,bf_no = '$bf_no'
                    ,mb_id = '$mb_id'
                    ,bf_source = '$upload[source]'
                    ,bf_file = '$upload[file]'
                    ,bf_filesize = '$upload[filesize]'
                    ,bf_width = '$timg[0]'
                    ,bf_height = '$timg[1]'
                    ,bf_type = '$timg[2]'
                    ,bf_datetime = '$g4[time_ymdhis]'
                    ,bf_ip = '$_SERVER[REMOTE_ADDR]'";
    $qry = sql_query($sql, false);
    if (!$qry) { // guploader 테이블이 없다면 생성
        $sql_table = "create table $mw[guploader_table] (
            id int not null auto_increment,
            bo_table varchar(20) not null,
            bf_no int not null,
            mb_id varchar(20) not null,
            bf_source varchar(255) not null,
            bf_file varchar(255) not null,
            bf_filesize int not null,
            bf_width int not null,
            bf_height int not null,
            bf_type tinyint not null,
            bf_datetime datetime not null,
            bf_ip varchar(20) not null,
            primary key (id),
            index (bo_table, mb_id, bf_no)
        )";
        $qry = sql_query($sql_table, false);
        $qry = sql_query($sql);
    }
}
?>
