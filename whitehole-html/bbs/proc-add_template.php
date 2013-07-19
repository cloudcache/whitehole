<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$name=$_POST['name'];
$description=$_POST['description'];
$public=$_POST['chk_public'];
$featured=$_POST['chk_featured'];
$hypervisor=$_POST['chk_hypervisor'];
$bits=$_POST['chk_bits'];
$os_type=$_POST['chk_os_type'];
$url=$_POST['url'];
$bootable=$_POST['chk_bootable'];

/*
echo $name;
echo "<br>";
echo $description;
echo "<br>";
echo $public;
echo "<br>";
echo $featured;
echo "<br>";
echo $hypervisor;
echo "<br>";
echo $bits;
echo "<br>";
echo $url;
echo "<br>";
echo $bootable;
echo "<br>";
exit;
*/

$account=$loguser;
$create_time=time();
$template_path="/home/mnt/sec/templates";
$uuid=rtrim(shell_exec("uuidgen"));

$vdi_file="$template_path/$uuid";
$return=rtrim(run_ssh_key('localhost','root',"mkdir -p $template_path; wget $url -O $vdi_file 2> /dev/null; echo $?"));
if ((int)$return!=0) {
	run_ssh_key('localhost','root',"rm -f $vdi_file");
	alert_msg("Can't download template-image... Check the URL.");
	exit;
}
$img_info=explode(' ',run_ssh_key('localhost','root',"qemu-img info $vdi_file | tr '\n' ' '"));

$format=$img_info['4'];
$size_virtual=ereg_replace('G','',$img_info['7']);
$size_real=ereg_replace('G','',$img_info['12']);

$size_verify=rtrim(run_ssh_key('localhost','root',"ls -l $vdi_file | cut -d' ' -f5"));

$query="insert into vm_template value ('$uuid','$name','$public','$featured','$hypervisor','$bits','$url','$format','$create_time','$account','$description','$bootable','$size_virtual','$size_real','$size_verify','$os_type')";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
} else {
	echo ("
		<script language=\"javascript\">
			location.href=\"view_template.php\"
		</script>
	");
}
?>
