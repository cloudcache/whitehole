<?php
include_once("./_common.php");

// 게시판 관리자 이상 복사, 이동 가능
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super') 
    alert_close("게시판 관리자 이상 접근이 가능합니다.");

if ($sw != "move" && $sw != "copy")
    alert("sw 값이 제대로 넘어오지 않았습니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

mw_move($wr_id_list, $chk_bo_table, $sw);

$msg = "해당 게시물을 선택한 게시판으로 $act 하였습니다.";
$opener_href = "$g4[bbs_path]/board.php?bo_table=$bo_table&page=$page&$qstr";

echo <<<HEREDOC
<meta http-equiv='content-type' content='text/html; charset={$g4['charset']}'> 
<script type="text/javascript">
alert("{$msg}");
//opener.document.location.href = "{$opener_href}";
opener.document.location.reload();
window.close();
</script>
HEREDOC;
