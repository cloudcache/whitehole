<?
## admin 체크
if ($member['mb_level']<10){
    $msg = "[접근 거부]";
    if ($cwin)
        alert_close($msg);
    else
        alert($msg, "$g4_path/bbs/login.php?wr_id=$wr_id{$qstr}&url=$g4_path/index.php");
}
?>
