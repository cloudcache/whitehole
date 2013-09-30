<? include_once("./_head.whitehole.php"); ?>
<?

include "../db_conn.php";
include "../functions.php";

$template_path="/home/mnt/sec/templates";
$primary_base_path="/home/mnt/pri/base";

$uuid=$_GET['uuid'];

$query_pre="select count(origin) from info_vm where origin like 'T___$uuid'";
$result_pre=@mysql_query($query_pre);
if (!$result) {
    Query_Error();
}
$data_pre=@mysql_fetch_row($result_pre);
$used_count=$data_pre['0'];
if ($used_count != 0) {
    alert_msg("[Error] ëì Templateì´ë¯¸ì§ë ì¼ë¶ VMì ìí´ ì¬ì©ì¤ì ììµëë¤.");
    exit;
}

$query="delete from vm_template where uuid='$uuid'";
$result=@mysql_query($query);

if (!$result) {
    Query_Error();
} else {
    run_ssh_key('localhost','root',"rm -f $template_path/$uuid $primary_base_path/$uuid");
    echo ("
        <script language=\"javascript\">
            location.href=\"view_template.php\"
        </script>
    ");
}
?>
