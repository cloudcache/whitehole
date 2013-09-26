<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";
include "gen_mac.php";

$iso_path="/home/mnt/sec/iso";

$iso_uuid=$_POST['iso_uuid'];
#print_r($iso_uuid);
#exit;

$query_iso_desc="select name,os_type from iso where uuid='$iso_uuid'";
$result_iso_desc=@mysql_query($query_iso_desc);
if (!$result_iso_desc) {
	Query_Error();
}
$data_iso_desc=@mysql_fetch_row($result_iso_desc);

$iso_name=$data_iso_desc[0];
$iso_os_type=$data_iso_desc[1];
$iso_file="$iso_path/$iso_uuid.iso";

$core=$_POST['core'];
$memory=$_POST['memory']*1024;
$root_volume=$_POST['root_volume'];
$data_volume=$_POST['data_volume'];
$ssh_keypair_uuid=$_POST['ssh_keypair_uuid'];

if (!$ssh_keypair_uuid) {
	alert_msg("[Error] SSH-Kyepir 개인키가 선택되지 않았습니다.");
	exit;
}

$query_sshkey_desc="select description from ssh_keypair where uuid='$ssh_keypair_uuid'";
$result_sshkey_desc=@mysql_query($query_sshkey_desc);
if (!$result_sshkey_desc) {
	Query_Error();
}
$data_sshkey_desc=@mysql_fetch_row($result_sshkey_desc);
$ssh_keypair_desc=$data_sshkey_desc[0];

$security_group_uuid=$_POST['security_group_uuid'];
if (!$security_group_uuid) {
	alert_msg("[Error] Security Group이 선택되지 않았습니다.");
	exit;
}
$query="select rule_name from security_group where uuid='$security_group_uuid'";
$result=@mysql_query($query);
if (!$result) {
    Query_Error();
}
$data=@mysql_fetch_row($result);
$security_group_rule_name=$data['0'];

$create_time=time();

// VM 배치할 타겟노드 선별
$query_memory=$memory/1024;
#echo $query_memory;
run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php");
#$query="select ip_address,max(free_sys_cpu) from info_node where free_sys_mem>$query_memory and hypervisor='$template_hypervisor'";
#$query="select ip_address,host_id from info_node where free_sys_cpu>30 and free_sys_mem>$query_memory and hypervisor='$template_hypervisor' order by rand() limit 1";
$query="select ip_address,host_id from info_node where free_sys_cpu>30 and free_sys_mem>$query_memory and hypervisor='kvm' order by free_sys_mem desc limit 1";
#$query="select ip_address,host_id,max(free_sys_mem) from info_node where hypervisor='$template_hypervisor'";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
$data=mysql_fetch_row($result);
$target_node=$data['0'];
if (!$target_node) {
	alert_msg("[Error] 물리 장비 선택이 불가능합니다. 확인 후 재시도 바랍니다.");
	exit;
}
#$host_id=$data['1'];

#echo $template_hypervisor;
#echo $target_node;
#exit;


// 필요 디렉토리 경로 정의
$path_pri="/home/mnt/pri";
$path_sec="/home/mnt/sec";

$path_nwfilter_xml="/home/mnt/sec/xml-nwfilter";

// VM 이미지 생성 (Root-Device)
$vm_uuid=rtrim(shell_exec("uuidgen"));


#########################################################################################

## 네트워크 풀 테이블명 정의
$table_network_pool="network_pool";


//트랜잭션
$success=true;

$result_select_ip=@mysql_query("set autocommit=0");
$result_select_ip=@mysql_query("begin");

$query_select_ip="select ip_address from $table_network_pool where used='0' order by rand() limit 1";

$result_select_ip=@mysql_query($query_select_ip);
if (!$result_select_ip) {
	$success=false;
}
$data_select_ip=mysql_fetch_row($result_select_ip);

$vm_ip_address=$data_select_ip['0'];
if (!$vm_ip_address) {
	$success=false;
}
$query_update_ip="update $table_network_pool set used='1',vm='$vm_uuid',account='$loguser' where ip_address='$vm_ip_address'";
$result_update_ip=@mysql_query($query_update_ip);
if (!$result_select_ip) {
	$success=false;
}

## VM 호스트네임 네이밍
$vm_ip_address_array=explode('.',$vm_ip_address);
$vm_gateway="$vm_ip_address_array[0].$vm_ip_address_array[1].$vm_ip_address_array[2].1";
$vm_network="$vm_ip_address_array[0].$vm_ip_address_array[1].$vm_ip_address_array[2].0";
$vm_broadcast="$vm_ip_address_array[0].$vm_ip_address_array[1].$vm_ip_address_array[2].255";
$vm_netmask="255.255.255.0";

if ($_POST['hostname']) {
	$vm_name="$member[mb_id]"."-".$_POST['hostname'];
} else {
	$vm_name="vm-$loguser-".preg_replace("/\./","-",$vm_ip_address);
}

$query_chk_vm_name="select count(*) from info_vm where name='$vm_name'";
$result_chk_vm_name=@mysql_query($query_chk_vm_name);
$data_chk_vm_name=mysql_fetch_row($result_chk_vm_name);
if ($data_chk_vm_name['0'] != "0") {
	$result_select_ip=@mysql_query("rollback");
	alert_msg("지정한 Hostname이 이미 존재합니다. 다시 시도하세요.");
	exit;
}

## Mac 주소 생성
$obj = new MACAddress;
$vm_mac=$obj->_generateXenMAC();
$vm_mac_2=$obj->_generateXenMAC();


#########################################################################################

// DNS Update
run_ssh_key('localhost','root',"/home/whitehole/update-dns.sh create $dns_server $vm_name.test.org $vm_ip_address");

$path_instance="$path_pri/instances";
run_ssh_key('localhost','root',"mkdir -p $path_instance 2> /dev/null; qemu-img create -f qcow2 $path_instance/$vm_uuid ${root_volume}G");

if ($data_volume!=0) {
	if("$template_hypervisor"=="xen") {
		run_ssh_key('localhost','root',"qemu-img create -f raw $path_instance/$vm_uuid.data ${data_volume}G");
	} else {
		run_ssh_key('localhost','root',"qemu-img create -f qcow2 $path_instance/$vm_uuid.data ${data_volume}G");
	}
}

$ssh_key_pub_contents_tmp=explode(' ',file_get_contents("$path_sec/ssh-keypair/$loguser/$ssh_keypair_uuid.pub", true));
$ssh_key_pub_contents=ereg_replace("/\n\r|\r\n/", "", $ssh_key_pub_contents_tmp[1]);


# nwfilter 등록
run_ssh_key('localhost','root',"echo \"<filter name='vm-$vm_uuid' chain='root'><filterref filter='clean-traffic'/></filter>\"> $path_nwfilter_xml/vm-$vm_uuid.xml");
run_ssh_key($target_node,'root',"virsh nwfilter-define $path_nwfilter_xml/vm-$vm_uuid.xml");

$query_sshkey_count="select used_count from ssh_keypair where uuid='$ssh_keypair_uuid'";
$result_sshkey_count=@mysql_query($query_sshkey_count);
if (!$result_sshkey_count) {
	Query_Error();
}
$date_sshkey_count=@mysql_fetch_row($result_sshkey_count);
$sshkey_count_update=$date_sshkey_count['0']+1;
$query_sshkey_count_update="update ssh_keypair set used_count='$sshkey_count_update'";
$result_sshkey_count_update=@mysql_query($query_sshkey_count_update);
if (!$result_sshkey_count_update) {
	Query_Error();
}

$query_security_group_count="select used_count from security_group where uuid='$security_group_uuid'";
$result_security_group_count=@mysql_query($query_security_group_count);
if (!$result_security_group_count) {
	Query_Error();
}
$date_security_group_count=@mysql_fetch_row($result_security_group_count);
$security_group_count_update=$date_security_group_count['0']+1;
$query_security_group_count_update="update security_group set used_count='$security_group_count_update' where uuid='$security_group_uuid'";
$result_security_group_count_update=@mysql_query($query_security_group_count_update);
if (!$result_security_group_count_update) {
	Query_Error();
}


$conn=libvirt_connect("qemu+ssh://root@$target_node/system","0");
$kvm_bin="/usr/bin/kvm";
$vm_arch="x86_64";

if ($data_volume==0) {
	$xml_str=<<<EOF
<domain type='kvm'>
  <name>$vm_name</name>
  <uuid>$vm_uuid</uuid>
  <memory>$memory</memory>
  <currentMemory>$memory</currentMemory>
  <cpu match='exact'>
    <model>Nehalem</model>
    <vendor>Intel</vendor>
      <feature policy='require' name='vmx'/>
  </cpu>
  <vcpu>$core</vcpu>
  <os>
    <type arch='$vm_arch'>hvm</type>
    <boot dev='hd'/>
    <boot dev='cdrom'/>
  </os>
  <features>
    <acpi/>
    <apic/>
    <pae/>
  </features>
  <clock offset='utc'/>
  <on_poweroff>destroy</on_poweroff>
  <on_reboot>restart</on_reboot>
  <on_crash>restart</on_crash>
  <devices>
    <emulator>$kvm_bin</emulator>
    <disk type='file' device='disk'>
      <driver name='qemu' type='qcow2' cache='none' io='threads'/>
      <source file='$path_instance/$vm_uuid'/>
      <target dev='vda' bus='virtio'/>
      <alias name='virtio-disk0'/>
    </disk>
    <disk type='file' device='cdrom'>
      <driver name='qemu' type='raw'/>
      <source file='$iso_file'/>
      <target dev='hdc' bus='ide'/>
      <readonly/>
      <alias name='ide0-1-0'/>
    </disk>
    <interface type='bridge'>
      <mac address='$vm_mac'/>
      <source bridge='br0'/>
      <model type='virtio'/>
      <filterref filter='vm-$vm_uuid'>
        <parameter name='IP' value='$vm_ip_address'/>
      </filterref>
      <alias name='eth0'/>
    </interface>
    <console type='pty' tty='/dev/pts/0'>
      <source path='/dev/pts/0'/>
      <target port='0'/>
    </console>
    <input type='mouse' bus='ps2'/>
    <graphics type='vnc' autoport='yes' listen='127.0.0.1'/>
  </devices>
</domain>
EOF;
} else {
	$xml_str=<<<EOF
<domain type='kvm'>
  <name>$vm_name</name>
  <uuid>$vm_uuid</uuid>
  <memory>$memory</memory>
  <currentMemory>$memory</currentMemory>
  <cpu match='exact'>
    <model>Nehalem</model>
    <vendor>Intel</vendor>
      <feature policy='require' name='vmx'/>
  </cpu>
  <vcpu>$core</vcpu>
  <os>
    <type arch='$vm_arch'>hvm</type>
    <boot dev='hd'/>
    <boot dev='cdrom'/>
    <bootmenu enable='yes'/>
  </os>
  <features>
    <acpi/>
    <apic/>
    <pae/>
  </features>
  <clock offset='utc'/>
  <on_poweroff>destroy</on_poweroff>
  <on_reboot>restart</on_reboot>
  <on_crash>restart</on_crash>
  <devices>
    <emulator>$kvm_bin</emulator>
    <disk type='file' device='disk'>
      <driver name='qemu' type='qcow2' cache='none' io='threads'/>
      <source file='$path_instance/$vm_uuid'/>
      <target dev='vda' bus='virtio'/>
      <alias name='virtio-disk0'/>
    </disk>
    <disk type='file' device='disk'>
      <driver name='qemu' type='qcow2' cache='none' io='threads'/>
      <source file='$path_instance/$vm_uuid.data'/>
      <target dev='vdb' bus='virtio'/>
      <alias name='virtio-disk1'/>
    </disk>
    <disk type='file' device='cdrom'>
      <driver name='qemu' type='raw'/>
      <source file='$iso_file'/>
      <target dev='hdc' bus='ide'/>
      <readonly/>
      <alias name='ide0-1-0'/>
    </disk>
    <interface type='bridge'>
      <mac address='$vm_mac'/>
      <source bridge='br0'/>
      <model type='virtio'/>
      <filterref filter='vm-$vm_uuid'>
        <parameter name='IP' value='$vm_ip_address'/>
      </filterref>
      <alias name='eth0'/>
    </interface>
    <console type='pty' tty='/dev/pts/0'>
      <source path='/dev/pts/0'/>
      <target port='0'/>
    </console>
    <input type='mouse' bus='ps2'/>
    <graphics type='vnc' autoport='yes' listen='127.0.0.1'/>
  </devices>
</domain>
EOF;
}

# VM 생성

$resource=libvirt_domain_define_xml($conn, $xml_str);
$result_create=libvirt_domain_create($resource);
if ($result_create != "1") {
	$success=false;
}

$dom=libvirt_domain_lookup_by_name($conn,"$vm_name");

## CDROM/Controller/BootDevice 해제
$xml_str=libvirt_domain_get_xml_desc($dom,"");
$doc=new SimpleXMLElement($xml_str);

foreach($doc->os->boot as $target)
{
    if($target['dev'] == 'cdrom') {
        $d=dom_import_simplexml($target);
        $d->parentNode->removeChild($d);
    }
}
foreach($doc->devices->disk as $target)
{
    if($target['device'] == 'cdrom') {
        $d=dom_import_simplexml($target);
        $d->parentNode->removeChild($d);
    }
}
foreach($doc->devices->controller as $target)
{
    if($target['type'] == 'ide') {
        $d=dom_import_simplexml($target);
        $d->parentNode->removeChild($d);
    }
}
$xml_str=$doc->asXml();
$resource=libvirt_domain_define_xml($conn, $xml_str);

## VNC 포트 번호 확인
#$xml_str=libvirt_domain_get_xml_desc($dom,"");
$json=json_encode(new SimpleXMLElement($xml_str));
$xml_array=json_decode($json,TRUE);
$vnc_port=@$xml_array['devices']['graphics']['@attributes']['port'];

# MRTG 설정
run_ssh_key('localhost','root',"mkdir /var/www/mrtg/$vm_uuid");
run_ssh_key('localhost','root',"echo 'Preparing MRTG.......' > /var/www/mrtg/$vm_uuid/index.html");
run_ssh_key('localhost','root',"cp /home/whitehole/mrtg.template.cfg /home/whitehole/mrtg.cfg/$vm_uuid.cfg");
run_ssh_key('localhost','root',"sed -i 's/__uuid__/$vm_uuid/g' /home/whitehole/mrtg.cfg/$vm_uuid.cfg");

$query_create="insert into info_vm values ('$vm_uuid','$create_time','$ssh_keypair_uuid','$ssh_keypair_desc','$vm_ip_address','$vm_name','$core','$memory','$vm_mac','64','kvm','$target_node','$vnc_port','$loguser','$data_volume','$iso_os_type','$hostname','1','$security_group_uuid','I___$iso_uuid','1')";

$result_create=@mysql_query($query_create);
if (!$result_create) {
	$success=false;
}

if (!$success) {
	$result_select_ip=@mysql_query("rollback");
	run_ssh_key($target_node,'root',"virsh nwfilter-undefine vm-$vm_uuid");
	run_ssh_key('localhost','root',"rm -f $path_nwfilter_xml/vm-$vm_uuid.xml");
	run_ssh_key('localhost','root',"rm -f $path_instance/$vm_uuid*; rm -rf /var/www/mrtg/$vm_uuid; rm -f /home/whitehole/mrtg.cfg/$vm_uuid.cfg");
	run_ssh_key('localhost','root',"/home/whitehole/update-dns.sh delete $dns_server $vm_name.test.org $vm_ip_address");
	$return_destroy=libvirt_domain_destroy($dom);
//	if($return_destroy!=1) {
//		alert_msg_close("Error : Failed to Destroy VM (by libvirt)");
//	}
	$return_undefine=libvirt_domain_undefine($dom);
//	if($return_undefine!=1) {
//		alert_msg_close("Error : Failed to Undefine VM (by libvirt)");
//	}
	alert_msg("[Error] 트랜잭션 실패 -> 모든 작업 롤백");
} else {
	$result_select_ip=@mysql_query("commit");

	# SG 정책 적용
$path_nwfilter_xml="/home/mnt/sec/xml-nwfilter";
#$security_group_uuid=$uuid;
include "include-apply_security_group.php";

	# 노드 정보 업데이트
	run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php");
	echo ("
		<script language=\"javascript\">
			location.href=\"iso_install.php?iso_name=$vm_name&iso_node=$target_node&iso_vnc_port=$vnc_port&iso_uuid=$vm_uuid&iso_ip=$vm_ip_address&iso_gateway=$vm_gateway&iso_netmask=$vm_netmask&iso_network=$vm_network&iso_broadcast=$vm_broadcast\"
		</script>
	");
}

/*
$result_create=@mysql_query($query_create);
if (!$result_create) {
	Query_Error();
} else {
	# 노드 정보 업데이트
	run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php");
	echo ("
		<script language=\"javascript\">
			location.href=\"view_vm.php\"
		</script>
	");
}
*/

?>
