<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

global $is_admin;

// 투표번호가 넘어오지 않았다면 가장 큰(최근에 등록한) 투표번호를 얻는다
if (!$po_id) 
{
    $po_id = $config[cf_max_po_id];

    if (!$po_id) return;
}

$po = sql_fetch(" select * from $g4[poll_table] where po_id = '$po_id' ");

if ($po[po_point]) $point = "<span class='point'>($po[po_point] point 적립)</span>";
?>
<style type="text/css">
.mw-poll { border:1px solid #e1e1e1; text-align:left; padding:0 0 10px 0; }
.mw-poll a:hover { text-decoration:underline; }
.mw-poll .subject { background:url(<?=$poll_skin_path?>/img/box-bg.gif); height:24px; margin:0 0 7px 0; }
.mw-poll .subject { font-size:12px; color:#555; font-weight:bold; letter-spacing:-1px; text-decoration:none; text-align:left; }
.mw-poll .subject div { margin:5px 0 0 10px;}
.mw-poll table { margin:0 0 0 5px;}
.mw-poll .question { margin:10px 5px 10px 5px; text-align:left; }
.mw-poll .button { text-align:center; }
.mw-poll .point { font-weight:normal; font-size:11px; color:#888; }
</style>

<div class="mw-poll">
<div style="border:1px solid #fff;">

<form name="fpoll" method="post" action="<?=$g4[bbs_path]?>/poll_update.php" onsubmit="return fpoll_submit(this);" target="winPoll">
<input type="hidden" name="po_id" value="<?=$po_id?>">
<input type="hidden" name="skin_dir" value="<?=$skin_dir?>">

<div class="subject"><div>설문조사 <?=$point?></div></div>
<div class="question"><?=$po[po_subject]?></div>

<table border="0" cellspacing="0" cellpadding="0">
<? for ($i=1; $i<=9 && $po["po_poll{$i}"]; $i++) { ?>
<tr>
    <td width="10" height="20"><input type="radio" name="gb_poll" value="<?=$i?>" id='gb_poll_<?=$i?>'></td>
    <td><label for='gb_poll_<?=$i?>'><?=$po['po_poll'.$i]?></label></td>
</tr>
<? } ?>
</table>
<br/>
<div class="button">
<input type="image" src="<?=$poll_skin_path?>/img/poll_button.gif" width="70" height="25" border="0">
<a href="javascript:;" onclick="poll_result('<?=$po_id?>');"><img src="<?=$poll_skin_path?>/img/poll_view.gif" width="70" height="25" border="0"></a>
</div>
</form>

</div>
</div>

<script language='JavaScript'>
function fpoll_submit(f)
{
    var chk = false;
    for (i=0; i<f.gb_poll.length;i ++) {
        if (f.gb_poll[i].checked == true) {
            chk = f.gb_poll[i].value;
            break;
        }
    }

    <?
    if ($member[mb_level] < $po[po_level])
        echo " alert('권한 $po[po_level] 이상의 회원만 투표에 참여하실 수 있습니다.'); return false; ";
    ?>

    if (!chk) {
        alert("항목을 선택하세요");
        return false;
    }

    win_poll();
    return true;
}

function poll_result(po_id)
{
    <?
    if ($member[mb_level] < $po[po_level])
        echo " alert('권한 $po[po_level] 이상의 회원만 결과를 보실 수 있습니다.'); return false; ";
    ?>

    win_poll("<?=$g4[bbs_path]?>/poll_result.php?po_id="+po_id+"&skin_dir="+document.fpoll.skin_dir.value);
}
</script>
