<?
include "../db_conn.php";
include "../functions.php";

$path_nwfilter_xml="/home/mnt/sec/xml-nwfilter";
$security_group_uuid=$_GET['security_group_uuid'];

include "include-apply_security_group.php";

echo ("
    <script language=\"javascript\">
        location.href=\"edit_security_group.php?rule_name=$rule_name&uuid=$uuid&account=$account\"
    </script>
");
?>
