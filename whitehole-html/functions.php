<?
//////////////////////////
// Logined User
//$account="admin";
//$account=$member['mb_id'];
$localIP = "@_LOCAL_IP_@";
$dns_server=$localIP;
$loguser=@$member['mb_id'];
### loguser는 웹사이트에 로그인한 유져이며,
### account는 VM/SSH-Key등 IaaS인프라의 소유자와 같은 관점에서 account를 의미.
//$account_id="1";
//echo $member['mb_id'];
#$account_pass="1234";

#$account="call518";
#$account_id="2";
##$account_pass="4321";

#$ucarp_pass="abcd1234";
//////////////////////////

//echo "
//<script type=\"text/javascript\">
//function goPage(url){
//   var winFeatures = \"toolbar=no,\" + \"location=no,\" + \"directories=no,\" + \"status=yes,\" + \"menubar=yes,\" +  \"scrollbars=yes,\" + \"resizable=yes,\" + \"width=1024, height=750\";
//   window.open(url,'popup', winFeatures); 
//}
//</script>"

/*
function alert_msg($error_message) {
	echo ("
		<script language=\"javascript\">
			alert(\"$error_message\");
			history.go(-1);
		</script>
	");
}
function alert_msg_close($error_message) {
	echo ("
		<script language=\"javascript\">
			alert(\"$error_message\");
			top.close();
		</script>
	");
}

*/
function alert_msg($error_message) {
	echo ("
		<script>
		$(document).ready(function()
		{
			alert('$error_message');
			history.go(-1);
		});
		</script>
	");
	@mysql_query("rollback");
	exit;
}

function alert_msg_close($error_message) {
	echo ("
		<script>
		$(document).ready(function()
		{
			alert('$error_message');
			top.close();
		});
		</script>
	");
	@mysql_query("rollback");
	exit;
}

function bail($msg, $error_code = 1) {
		printf("[Error $error_code in %s] $msg\n", basename($_SERVER['SCRIPT_NAME']));
		exit($error_code);
}

function Query_Error() {
	$ErrorNumber = @mysql_errno();
	$ErrorString = @mysql_error();
	$ErrorNoString = "DB Error:" . $ErrorNumber . "-" . $ErrorString;
	@mysql_query("rollback");
	alert_msg($ErrorNoString);
}

/*
function Query_Error_Unlock() {
	$ErrorNumber = @mysql_errno();
	$ErrorString = @mysql_error();
	$ErrorNoString = "DB Error:" . $ErrorNumber . "-" . $ErrorString;
	table_unlock();
	alert_msg_close($ErrorNoString);
#	exit;
}
*/

function run_ssh_key($ssh_host, $ssh_user, $ssh_cmd)
{
	$key_public="/var/www/.ssh/id_rsa.pub";
	$key_private="/var/www/.ssh/id_rsa";
	$connection = ssh2_connect($ssh_host,'22');
	ssh2_auth_pubkey_file($connection, $ssh_user, $key_public, $key_private);
	$stream = ssh2_exec($connection, "$ssh_cmd");
	stream_set_blocking($stream, true);
	return fread($stream, 20480);
}

function run_ssh_pass($ssh_host, $ssh_user, $ssh_pass, $ssh_cmd)
{
	$connection = ssh2_connect($ssh_host,'22');
	ssh2_auth_password($connection, $ssh_user, $ssh_pass);
	$stream = ssh2_exec($connection, $ssh_cmd);
	stream_set_blocking($stream, true);
	return fread($stream, 20480);
}

function run_scp_key($ssh_host, $ssh_user, $localfile, $remotefile)
{
	$key_public="/var/www/.ssh/id_rsa.pub";
	$key_private="/var/www/.ssh/id_rsa";
	$connection = ssh2_connect($ssh_host,'22');
	ssh2_auth_pubkey_file($connection, $ssh_user, $key_public, $key_private);
	ssh2_scp_send($connection, $localfile, $remotefile);
}

function run_scp_pass($ssh_host, $ssh_user, $ssh_pass, $localfile, $remotefile)
{
	$connection = ssh2_connect($ssh_host,'22');
	ssh2_auth_password($connection, $ssh_user, $ssh_pass);
	ssh2_scp_send($connection, $localfile, $remotefile);
}

/*
function table_lock($table_name)
{
	$query_lock="lock tables $table_name read";
	$result_lock=@mysql_query($query_lock);
	if (!$result_lock) {
		Query_Error();
	}
}

function table_unlock()
{
	$query_unlock="unlock tables";
	$result_unlock=@mysql_query($query_unlock);
	if (!$result_unlock) {
		Query_Error();
	}
}
*/

?>
