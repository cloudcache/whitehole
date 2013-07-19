<?
include_once("_common.php");

$g4[title] = "포인트 정책";
include_once("_head.php");

$rowspan = array();

$idx = 0;

$sql = "select * from $g4[group_table] order by gr_id";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    $sql2 = "select * from $g4[board_table] where gr_id = '$row[gr_id]' order by bo_order_search ";
    $qry2 = sql_query($sql2);
    while ($row2 = sql_fetch_array($qry2)) {
        if ($row2[bo_read_level] >= 10 && $row2[bo_write_level] >= 10 &&$row2[bo_comment_level] >= 10 && $row2[bo_download_level] >= 10) continue;
        $list[$idx] = $row2; 
        $list[$idx][href] = "$g4[bbs_path]/board.php?bo_table=$row2[bo_table]";
        $list[$idx][gr_id] = $row[gr_id];
        $list[$idx][gr_subject] = $row[gr_subject];
        if ($row2[bo_read_level] == 1) $list[$idx][bo_read_point] = 0;
        if ($row2[bo_download_level] == 1) $list[$idx][bo_download_point] = 0;
        if ($row2[bo_write_level] == 1) $list[$idx][bo_write_point] = 0;
        if ($row2[bo_comment_level] == 1) $list[$idx][bo_comment_point] = 0;
        $rowspan[$row[gr_id]] += 1;
        $idx++;
    }
}
$total_count = sizeof($list);

?>
<style type="text/css">
.info { height:25px; margin:0 0 0 10px; font-size:13px; }
.point-policy { background-color:#ddd; }
.point-policy td { background-color:#fff; }
.point-policy .head { height:30px; text-align:center; font-weight:bold; background-color:#fafafa; }
.point-policy .body { height:25px; text-align:center; }
.point-policy .body.right { text-align:right; padding-right:10px; }
.point-policy .body.left { text-align:left; padding-left:10px; }
.point-policy .body a:hover { text-decoration:underline; }
</style>

<?
if ($config[cf_register_point]) echo "<div class='info'>· 회원가입 포인트 : <strong>".number_format($config[cf_register_point])."</strong> 점</div>";
if ($config[cf_login_point]) echo "<div class='info'>· 로그인 포인트 : <strong>".number_format($config[cf_login_point])."</strong> 점</div>";
?>

<table border=0 cellpadding=0 cellspacing=1 width=100% class="point-policy">
<colgroup width="100"/>
<colgroup width=""/>
<colgroup width="80"/>
<colgroup width="80"/>
<colgroup width="80"/>
<colgroup width="80"/>
<tr>
    <td class="head"> 서비스 </td>
    <td class="head"> 메뉴 </td>
    <td class="head"> 글읽기 </td>
    <td class="head"> 글쓰기 </td>
    <td class="head"> 코멘트 쓰기 </td>
    <td class="head"> 다운로드 </td>
</tr>
<? for ($i=0; $i<$total_count; ++$i) { ?> 
<tr>
    <? if ($list[$i-1][gr_subject] != $list[$i][gr_subject]) { ?>
	<td class="body" rowspan="<?=$rowspan["{$list[$i][gr_id]}"]?>"> <?=$list[$i][gr_subject]?> </td>
    <? } ?>
    <td class="body left"> <a href="<?=$list[$i][href]?>"><?=$list[$i][bo_subject]?></a> </td>
    <td class="body right"> <?=$list[$i][bo_read_point]?> 점 </td>
    <td class="body right"> <?=$list[$i][bo_write_point]?> 점 </td>
    <td class="body right"> <?=$list[$i][bo_comment_point]?> 점 </td>
    <td class="body right"> <?=number_format($list[$i][bo_download_point])?> 점 </td>
</tr>
<? } ?>
</table>

<?
include_once("_tail.php");
?>
