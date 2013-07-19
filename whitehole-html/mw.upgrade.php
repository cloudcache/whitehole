<?php
if (!$dblink) exit;

@mysql_query("alter table $g4[board_table] change `bo_count_delete` `bo_count_delete` tinyint not null default '1'");
@mysql_query("alter table $g4[board_table] change `bo_count_modify` `bo_count_modify` tinyint not null default '1'");
@mysql_query("alter table $g4[board_table] change `bo_gallery_cols` `bo_gallery_cols` int not null default '4'");
@mysql_query("alter table $g4[board_table] change `bo_table_width` `bo_table_width` int not null default '100'");
@mysql_query("alter table $g4[board_table] change `bo_page_rows` `bo_page_rows` int not null default '20'");
@mysql_query("alter table $g4[board_table] change `bo_subject_len` `bo_subject_len` int not null default '60'");
@mysql_query("alter table $g4[board_table] change `bo_new` `bo_new` int not null default '24'");
@mysql_query("alter table $g4[board_table] change `bo_hot` `bo_hot` int not null default '100'");
@mysql_query("alter table $g4[board_table] change `bo_image_width` `bo_image_width` int not null default '600'");
@mysql_query("alter table $g4[board_table] change `bo_upload_count` `bo_upload_count` tinyint not null default '600'");
@mysql_query("alter table $g4[board_table] change `bo_upload_size` `bo_upload_size` int not null default '1048576'");
@mysql_query("alter table $g4[board_table] change `bo_reply_order` `bo_reply_order` tinyint not null default '1'");
@mysql_query("alter table $g4[board_table] change `bo_use_search` `bo_use_search` tinyint not null default '1'");
@mysql_query("alter table $g4[board_table] change `bo_skin` `bo_skin` varchar(255) not null default 'mw.basic'");
@mysql_query("alter table $g4[board_table] change `bo_disable_tags` `bo_disable_tags` varchar(255) not null default 'script|iframe'");
@mysql_query("alter table $g4[board_table] change `bo_use_secret` `bo_use_secret` tinyint not null default '1'");
@mysql_query("alter table $g4[board_table] change `bo_include_head` `bo_include_head` varchar(255) not null default '_head.php'");
@mysql_query("alter table $g4[board_table] change `bo_include_tail` `bo_include_tail` varchar(255) not null default '_tail.php'");
@mysql_query("alter table $g4[group_table] change `gr_1_subj` `gr_1_subj` varchar(255) not null default '출력순서'");
@mysql_query("alter table $g4[group_table] change `gr_2_subj` `gr_2_subj` varchar(255) not null default '사용자정의URL'");

$bo_order_search = 0;
for ($g=1; $g<=5; $g++) {
    $gr_id = sprintf("%02d", $g);
    @mysql_query("insert into $g4[group_table] set gr_id = 'G{$gr_id}', gr_subject = 'G{$gr_id}', gr_1 = '$g'");
    for ($b=1; $b<=3; $b++) {
        $bo_table =  "B{$g}{$b}";
        $bo_order_search++;
        @mysql_query("insert into $g4[board_table] set gr_id = 'G{$gr_id}', bo_table = '$bo_table', bo_subject = '$bo_table', bo_order_search = '$bo_order_search'");

        $create_table = "$g4[write_prefix]$bo_table";

        $file = file("../adm/sql_write.sql");
        $sql = implode($file, "\n");

        $source = array("/__TABLE_NAME__/", "/;/");
        $target = array($create_table, "");
        $sql = preg_replace($source, $target, $sql);
        @mysql_query($sql);

        $board_path = "$g4[path]/data/file/$bo_table";

        // 게시판 디렉토리 생성
        @mkdir($board_path, 0707);
        @chmod($board_path, 0707);

        // 디렉토리에 있는 파일의 목록을 보이지 않게 한다.
        $file = $board_path . "/index.php";
        $f = @fopen($file, "w");
        @fwrite($f, "");
        @fclose($f);
        @chmod($file, 0606);
    }
}

@mysql_query("insert into $g4[poll_table] set po_subject = '지금 막 새로 설치한 배추빌더 어때요?', po_poll1 = '좋아요', po_poll2 = '멋져요', po_poll3 = '이뻐요', po_poll4 = '상큼해요', po_point = '1000', po_date = '$g4[time_ymd]', po_level = '1'");
@mysql_query(" update $g4[config_table] set cf_max_po_id = '1' ");

