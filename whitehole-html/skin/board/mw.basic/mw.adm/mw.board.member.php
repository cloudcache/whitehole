<?
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

// 접근권한 목록 v.1.0.2 패치
$sql = "alter table $mw[board_member_table] drop primary key , add primary key (bo_table, mb_id)";
sql_query($sql, false);

if ($is_admin != "super")
    alert("접근 권한이 없습니다.");

$admin_menu[board_member] = "select";

$sfl = "mb_id";
$colspan = 5;

$sql_common = " from $mw[board_member_table] ";
$sql_order = " order by bm_datetime desc ";
$sql_search = " where bo_table = '$bo_table' ";

if ($sfl && $stx)
    $sql_search .= " and $sfl like '%$stx%' ";

$sql = "select count(*) as cnt
        $sql_common
        $sql_search";
$row = sql_fetch($sql);
$total_count = $row[cnt];

$rows = $config[cf_write_pages];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select *
        $sql_common
        $sql_search
        $sql_order
        limit $from_record, $rows ";
$qry = sql_query($sql);

$list = array();
for ($i=0; $row = sql_fetch_array($qry); ++$i) {
    $list[$i] = $row;
    $list[$i][num] = $total_count - ($page - 1) * $rows - $i;

    $mb = get_member($row[mb_id], "mb_id, mb_nick, mb_homepage, mb_email");
    $list[$i][name] = get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_homepage], $mb[mb_email]);
}

$write_pages = get_paging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?bo_table=$bo_table{$qstr}&page=");

//$g4[title] = "배추 BASIC SKIN 접근권한 설정";
include_once("$g4[path]/head.sub.php");
?>
<script type="text/javascript" src="<?=$g4[path]?>/js/sideview.js"></script>

<style type="text/css">
input.ed { height:20px; border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
textarea { border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
input.bt { background-color:#efefef; height:20px; cursor:pointer; font-size:11px; font-family:dotum; }
</style>

<div style="height:30px; background-color:#fff;">
    <form name="fwrite" method=post action="mw.board.member.update.php" style="margin:5px 0 5px 5px; float:left;">
    <input type=hidden name=bo_table value="<?=$bo_table?>">
    회원ID : <input type=text size=15 class=ed name=mb_id required itemname="회원ID">
    <input type=submit value="등록" class="bt">
    </form>

    <form name="fsearch" method=get action="<?=$PHP_SELF?>" style="margin:5px 5px 5px 0; float:right;">
    <input type=hidden name=bo_table value="<?=$bo_table?>">
    회원ID : <input type=text size=15 name=stx class=ed required itemname="회원ID" value="<?=$stx?>">
    <input type=submit value="검색" class="bt">
    <input type=button value="처음" class="bt" onclick="location.href='mw.board.member.php?bo_table=<?=$bo_table?>'">
    </form>
</div>

<table border=0 width=100% align=center cellspacing=1 bgcolor="#dddddd">
<colgroup width=60>
<colgroup width=120>
<colgroup width=120>
<colgroup width=''>
<colgroup width=50>
<tr align=center height=30 bgcolor="#efefef" style="font-weight:bold;">
    <td> 번호 </td>
    <td> 회원ID </td>
    <td> 회원정보 </td>
    <td> 처리일시 </td>
    <td> 삭제 </td>
</tr>
<? foreach ($list as $row) {?>
<tr align=center height=30 bgcolor="#ffffff">
    <td> <?=$row[num]?> </td>
    <td> <?=$row[mb_id]?> </td>
    <td> <?=$row[name]?> </td>
    <td> <?=$row[bm_datetime]?> </td>
    <td> <a href="javascript:mw_del('<?=$row[mb_id]?>');"><img src="<?=$g4[admin_path]?>/img/icon_delete.gif" align=absmiddle></a> </td>
</tr>
<? } ?>
<? if (!$total_count) { ?>
<tr><td colspan=<?=$colspan?> height=100 align=center bgcolor="#ffffff">등록된 회원ID가 없습니다.</td></tr>
<? } ?>
</table>

<div style="margin:20px 0 0 0; text-align:center;"> <?=$write_pages?> </div>

<div style="height:50px;"></div>

<script type="text/javascript">
function mw_del(mb_id) {
    if (confirm("정말 삭제하시겠습니까?")) {
        location.href = "mw.board.member.update.php?w=d&bo_table=<?=$bo_table.$qstr?>&mb_id=" + mb_id;
    }
}
</script>

<?
include_once("$g4[path]/tail.sub.php");
?>
