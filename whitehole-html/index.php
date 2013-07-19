<?
include_once("./_common.php");

$g4['title'] = "";
include_once("./_head.php");
?>

<style type="text/css">
.item { margin:0 0 10px 0; }
</style>

<!--
        <script language="javascript">
            location.href="./bbs/view_vm.php"
        </script>
-->

<table border="0" cellpadding="0" cellspacing="0" width="800">
<?
$sql = "select * from $g4[board_table] order by bo_order_search";
$qry = sql_query($sql);
for ($i=0; $row=sql_fetch_array($qry); $i++) {
?>
<tr>
    <td width="700" valign="top">
        <div class="item"><?=latest("mw.list", $row[bo_table], 20, 100)?></div>
    </td>
<!--
    <td width="10"></td>
    <td width="345" valign="top">
        <?
        $row = sql_fetch_array($qry); $i++;
        if ($row) {
        ?>
        <div class="item"><?=latest("mw.list", $row[bo_table], 5, 45)?></div>
        <? } ?>
    </td>
-->
</tr>
<? } ?>
</table>

<?
include_once("./_tail.php");
//echo $member['mb_id'];
?>
</body>
</html>
