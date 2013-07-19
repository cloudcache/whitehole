<?php
/**
 * 스마트알람 (Smart-Alarm for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */


if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

if ($bo_table && $is_member && function_exists('mw_moa_104')) {

$moa_check = 'checked';
if (!$moa_config) {
    $moa_config = sql_fetch("select * from $mw_moa_config_table where mb_id = '$member[mb_id]' ", false);
    if (strstr($moa_config[cf_reject_board], "$bo_table,")) {
        $moa_check = '';
    }
}
?>
<script type="text/javascript">
function smart_alarm_config (chk)
{
    var arm = 0;

    if (chk.checked) arm = 1;
    if (!arm) {
        if (!confirm("정말 이 게시판의 스마트 알람을 꺼두시겠습니까?")) {
            chk.checked = true;
        }
    }
    $.get("<?=$moa_path?>/board_config.php?bo_table=<?=$bo_table?>&arm="+arm);
}
</script>
<span style="color:#888; font:normal 11px 'gulim'">
[<input type="checkbox" name="smart-alarm" value="1" onclick="smart_alarm_config(this)" <?=$moa_check?>>알람]
</span>
<? }

