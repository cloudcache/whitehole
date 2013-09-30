<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";
include "gen_mac.php";

#$args=explode('/',$_POST['args']);
#$template_uuid=$args['0'];
#$template_size_verify=$args['1'];
#$template_hypervisor=$args['2'];
#$template_bits=$args['3'];
#$template_os_type=$args['4'];
#print_r($args);
#exit;


$template_uuid=$_POST['template_uuid'];
#print_r($template_uuid);
#exit;

$query="select description,size_verify,hypervisor,bits,os_type from vm_template where uuid='$template_uuid'";
$result=@mysql_query($query);

if (!$result) {
	Query_Error();
}
$data=@mysql_fetch_row($result);
#print_r($data);
#exit;

$template_description=$data['0'];
$template_size_verify=$data['1'];
$template_hypervisor=$data['2'];
$template_bits=$data['3'];
$template_os_type=$data['4'];


#$hostname="$member[mb_id]"."-".$_POST['hostname'];
#$hostname=$_POST['hostname'];
$core=$_POST['core'];
$memory=$_POST['memory']*1024;
$root_volume=$_POST['root_volume'];
$data_volume=$_POST['data_volume'];
$ssh_keypair_uuid=$_POST['ssh_keypair_uuid'];

if (!$ssh_keypair_uuid) {
	alert_msg("[Error] SSH-Kyepir 개인키가 선택되지 않았습니다.");
	exit;
}

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

/*
echo $template_uuid;
echo $template_size_verify;
echo $template_hypervisor;
echo $template_bits;
echo $core;
echo $memory;
echo $data_volume;
exit;
*/

#$pass=$account_pass;

$query_sshkey_desc="select description from ssh_keypair where uuid='$ssh_keypair_uuid'";
$result_sshkey_desc=@mysql_query($query_sshkey_desc);
if (!$result_sshkey_desc) {
	Query_Error();
}
$data_sshkey_desc=@mysql_fetch_row($result_sshkey_desc);
$ssh_keypair_desc=$data_sshkey_desc[0];

$create_time=time();

// VM 배치할 타겟노드 선별
$query_memory=$memory/1024;
#echo $query_memory;
#run_ssh_key('localhost','root',"/usr/bin/php /home/whitehole/cron.d/collect_node_status.php");
#$query="select ip_address,max(free_sys_cpu) from info_node where free_sys_mem>$query_memory and hypervisor='$template_hypervisor'";
#$query="select ip_address,host_id from info_node where free_sys_cpu>30 and free_sys_mem>$query_memory and hypervisor='$template_hypervisor' order by rand() limit 1";
$query="select ip_address,host_id from info_node where free_sys_cpu>30 and free_sys_mem>$query_memory and hypervisor='$template_hypervisor' order by free_sys_mem desc limit 1";
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

/*
## 테이블 락걸기 (동시 요청에 동일 IP 응답 방지 목적)
## IP 뽑기
#table_lock($table_network_pool);
$query_select_ip="select ip_address from $table_network_pool where used='0' order by rand() limit 1";
$result_select_ip=@mysql_query($query_select_ip);
if (!$result_select_ip) {
	Query_Error();
}
$data_select_ip=mysql_fetch_row($result_select_ip);
$vm_ip_address=$data_select_ip['0'];
if (!$vm_ip_address) {
	alert_msg("IP Resource(Network Pool) is depleted!!!!!!!!!");
	exit;
}
$vm_ip_address_array=explode('.',$vm_ip_address);
$vm_gateway="$vm_ip_address_array[0].$vm_ip_address_array[1].$vm_ip_address_array[2].1";
if ($hostname) {
	$vm_name="$hostname";
} else {
	$vm_name="vm-$loguser-".preg_replace('/\./','-',$vm_ip_address);
}
$obj = new MACAddress;
$vm_mac=$obj->_generateXenMAC();

$query_update_ip="update $table_network_pool set used='1',vm='$vm_uuid',account='$loguser' where ip_address='$vm_ip_address'";
$result_update_ip=@mysql_query($query_update_ip);
if (!$result_update_ip) {
	Query_Error();
}

## 뽑은 IP 사용됨 표시
##table_unlock();
*/

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

//if (!$success) {
//	$result_select_ip=@mysql_query("rollback");
//	alert_msg("Transaction Result: False");
//} else {
//	$result_select_ip=@mysql_query("commit");
//}

## VM 호스트네임 네이밍
$vm_ip_address_array=explode('.',$vm_ip_address);
$vm_gateway="$vm_ip_address_array[0].$vm_ip_address_array[1].$vm_ip_address_array[2].1";
$vm_network="$vm_ip_address_array[0].$vm_ip_address_array[1].$vm_ip_address_array[2].0";
$vm_broadcast="$vm_ip_address_array[0].$vm_ip_address_array[1].$vm_ip_address_array[2].255";

if ($_POST['hostname']) {
	$vm_name="$loguser"."-".$_POST['hostname'];
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

if("$template_hypervisor"=="xen") {
	$path_template="$path_sec/templates";
	$path_instance="$path_pri/instances";
	run_ssh_key('localhost','root',"mkdir -p $path_instance 2> /dev/null; cp $path_template/$template_uuid $path_instance/$vm_uuid");
	$size_verify_new=rtrim(run_ssh_key('localhost','root',"ls -l $path_template/$template_uuid | cut -d' ' -f5"));
	if ((int)$template_size_verify!=(int)$size_verify_new) {
		alert_msg("Image File-size is different....");
		exit;
	}
} else {
	$path_base="$path_pri/base";
	$path_template="$path_sec/templates";
	$path_instance="$path_pri/instances";
	// 선택된 Template 파일이 Secondary-Storage로부터 Primary-Storage의 base 디렉토리에 존재하는지 체크 및 복사
	#$chk_file_exist=rtrim(run_ssh_key('localhost','root',"test -e $path_base/$template_uuid; echo $?"));
	#if ("$chk_file_exist"=="0") {
	$chk_file_exist=file_exists("$path_base/$template_uuid");
	if ($chk_file_exist) {
		$size_verify_new=rtrim(run_ssh_key('localhost','root',"ls -l $path_base/$template_uuid | cut -d' ' -f5"));
		if ((int)$template_size_verify!=(int)$size_verify_new) {
			#alert_msg("Template-base already exist!!!.. but file-size is different....");
			run_ssh_key('localhost','root',"cp $path_template/$template_uuid $path_base/$template_uuid 2> /dev/null"); 
			#exit;
		}
	} else {
		run_ssh_key('localhost','root',"cp $path_template/$template_uuid $path_base/$template_uuid 2> /dev/null"); 
	}
	run_ssh_key('localhost','root',"mkdir -p $path_instance 2> /dev/null; qemu-img create -b $path_base/$template_uuid -f qcow2 $path_instance/$vm_uuid");
	run_ssh_key('localhost','root',"qemu-img resize $path_instance/$vm_uuid ${root_volume}G");
}

// DNS Update
run_ssh_key('localhost','root',"/home/whitehole/update-dns.sh create $dns_server $vm_name.@_DOMAIN_@ $vm_ip_address");


// Data Volume 생성
if ($data_volume!=0) {
	if("$template_hypervisor"=="xen") {
		run_ssh_key('localhost','root',"qemu-img create -f raw $path_instance/$vm_uuid.data ${data_volume}G");
	} else {
		run_ssh_key('localhost','root',"qemu-img create -f qcow2 $path_instance/$vm_uuid.data ${data_volume}G");
	}
}

## VM 이미지 내부 진입/설정 (네트워크 등등...)
do {
	$query_nbd="select * from nbd where used='0' order by rand() limit 1";
	$result_nbd=@mysql_query($query_nbd);
	if($result_nbd) {
		$data_nbd=mysql_fetch_row($result_nbd);
		$nbd_num=$data_nbd['0'];
		$query_nbd_used="update nbd set used='1' where num='$nbd_num'";
		@mysql_query($query_nbd_used);
		#run_ssh_key('localhost','root',"sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 1; mount /dev/mapper/nbd${nbd_num}p2 /mnt/nbd$nbd_num");
		#run_ssh_key('localhost','root',"sleep 3; kpartx -a /dev/nbd$nbd_num; sleep 3; mount /dev/mapper/nbd${nbd_num}p2 /mnt/nbd$nbd_num;  while [ `ls -l /mnt/nbd$nbd_num/ | wc -l` -le 2 ]; do sleep 2; done");
		if ("$template_os_type" == "RedHat") {
			#run_ssh_key('localhost','root',"sleep 3; mount /dev/nbd${nbd_num}p2 /mnt/nbd$nbd_num;  while [ `ls -l /mnt/nbd$nbd_num/ | wc -l` -le 2 ]; do sleep 2; done");
			#run_ssh_key('localhost','root',"sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 3; mount /dev/mapper/nbd${nbd_num}p2 /mnt/nbd$nbd_num; while [ `ls -l /mnt/nbd$nbd_num/ | wc -l` -le 2 ]; do sleep 2; done");
			#run_ssh_key('localhost','root',"sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 3; mount /dev/mapper/nbd${nbd_num}p2 /mnt/nbd$nbd_num");
			#run_ssh_key('localhost','root',"qemu-nbd -c /dev/nbd$nbd_num $path_instance/$vm_uuid; kpartx -a /dev/nbd$nbd_num; mount /dev/mapper/nbd${nbd_num}p2 /mnt/nbd$nbd_num");
                        $part_num="2";
                        run_ssh_key('localhost','root',"qemu-nbd -c /dev/nbd$nbd_num $path_instance/$vm_uuid");
                        run_ssh_key('localhost','root',"parted /dev/nbd$nbd_num --script rm $part_num");
                        $part_begin=rtrim(run_ssh_key('localhost','root',"parted /dev/nbd$nbd_num --script print free | tail -n2 | head -n1 | awk '{print $1}'"));
                        $part_end=rtrim(run_ssh_key('localhost','root',"parted /dev/nbd$nbd_num --script print free | tail -n2 | head -n1 | awk '{print $2}'"));
                        run_ssh_key('localhost','root',"parted /dev/nbd$nbd_num --script mkpart primary ext4 $part_begin $part_end 2>&1 > /dev/null");
                        run_ssh_key('localhost','root',"kpartx -a /dev/nbd$nbd_num; mount /dev/mapper/nbd${nbd_num}p$part_num /mnt/nbd$nbd_num");
		} else if ("$template_os_type" == "Debian") {
			#run_ssh_key('localhost','root',"sleep 3; mount /dev/nbd${nbd_num}p1 /mnt/nbd$nbd_num;  while [ `ls -l /mnt/nbd$nbd_num/ | wc -l` -le 2 ]; do sleep 2; done");
			#run_ssh_key('localhost','root',"sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 3; mount /dev/mapper/nbd${nbd_num}p1 /mnt/nbd$nbd_num; while [ `ls -l /mnt/nbd$nbd_num/ | wc -l` -le 2 ]; do sleep 2; done");
			#run_ssh_key('localhost','root',"sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 3; mount /dev/mapper/nbd${nbd_num}p1 /mnt/nbd$nbd_num");
			#run_ssh_key('localhost','root',"qemu-nbd -c /dev/nbd$nbd_num $path_instance/$vm_uuid; kpartx -a /dev/nbd$nbd_num; mount /dev/mapper/nbd${nbd_num}p1 /mnt/nbd$nbd_num");
                        $part_num="1";
                        run_ssh_key('localhost','root',"qemu-nbd -c /dev/nbd$nbd_num $path_instance/$vm_uuid");
                        run_ssh_key('localhost','root',"parted /dev/nbd$nbd_num --script rm $part_num");
                        run_ssh_key('localhost','root',"parted -a optimal /dev/nbd$nbd_num --script mkpart primary ext4 0% 100% 2>&1 > /dev/null");
                        run_ssh_key('localhost','root',"kpartx -a /dev/nbd$nbd_num; mount /dev/mapper/nbd${nbd_num}p$part_num /mnt/nbd$nbd_num");
		} else if ("$template_os_type" == "vSwitch") {
			#run_ssh_key('localhost','root',"sleep 3; mount /dev/nbd${nbd_num}p1 /mnt/nbd$nbd_num;  while [ `ls -l /mnt/nbd$nbd_num/ | wc -l` -le 2 ]; do sleep 2; done");
			#run_ssh_key('localhost','root',"sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 3; mount /dev/mapper/nbd${nbd_num}p1 /mnt/nbd$nbd_num; while [ `ls -l /mnt/nbd$nbd_num/ | wc -l` -le 2 ]; do sleep 2; done");
			#run_ssh_key('localhost','root',"sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 3; mount /dev/mapper/nbd${nbd_num}p1 /mnt/nbd$nbd_num");
			#run_ssh_key('localhost','root',"qemu-nbd -c /dev/nbd$nbd_num $path_instance/$vm_uuid; kpartx -a /dev/nbd$nbd_num; mount /dev/mapper/nbd${nbd_num}p1 /mnt/nbd$nbd_num");
                        $part_num="1";
                        run_ssh_key('localhost','root',"qemu-nbd -c /dev/nbd$nbd_num $path_instance/$vm_uuid");
                        run_ssh_key('localhost','root',"parted /dev/nbd$nbd_num --script rm $part_num");
                        run_ssh_key('localhost','root',"parted -a optimal /dev/nbd$nbd_num --script mkpart primary ext4 0% 100% 2>&1 > /dev/null");
                        run_ssh_key('localhost','root',"kpartx -a /dev/nbd$nbd_num; mount /dev/mapper/nbd${nbd_num}p$part_num /mnt/nbd$nbd_num");
		} else {
			alert_msg("[Error] 알수 없는 OS 타입입니다.");
		}
		break;
	}
	sleep(1);
} while (1);
$query_nbd_used_reset="update nbd set used='0' where num='$nbd_num'";
@mysql_query($query_nbd_used_reset);

function unmount_nbd($nbd_num) {
	#run_ssh_key('localhost','root',"umount /mnt/nbd$nbd_num; sleep 3; qemu-nbd -d /dev/nbd$nbd_num");
	#run_ssh_key('localhost','root',"umount /mnt/nbd$nbd_num; sleep 3; kpartx -d /dev/nbd$nbd_num; sleep 3; qemu-nbd -d /dev/nbd$nbd_num");
	run_ssh_key('localhost','root',"umount /mnt/nbd$nbd_num; kpartx -d /dev/nbd$nbd_num; qemu-nbd -d /dev/nbd$nbd_num");
}


if ("$template_os_type" == "RedHat") {
	## Networking 설정::RedHat
	run_ssh_key('localhost','root',"sed -i 's/HOSTNAME=.*/HOSTNAME=$vm_name.@_DOMAIN_@/g' /mnt/nbd$nbd_num/etc/sysconfig/network");

	## 임시: 여러가지...
	run_ssh_key('localhost','root',"sed -i '/172.21.19.116/d' /mnt/nbd$nbd_num/etc/yum.conf /mnt/nbd$nbd_num/etc/wgetrc; sed -i '/172.21.80.54/d' /mnt/nbd$nbd_num/etc/yum.conf /mnt/nbd$nbd_num/etc/wgetrc; sed -i 's/^Defaults    requiretty$/#Defaults    requiretty/g' /etc/sudoers /mnt/nbd$nbd_num/etc/sudoers; echo 'setterm -blank off' >> /mnt/nbd$nbd_num/etc/rc.local; echo 'resize2fs /dev/vda$part_num; sed -i \"/resize2fs/d\" /etc/rc.local' >> /mnt/nbd$nbd_num/etc/rc.local; sed -i '/172.21.19.15/d' /mnt/nbd$nbd_num/etc/ntp.conf; echo 'UseDNS no' >> /mnt/nbd$nbd_num/etc/ssh/sshd_config");
	
	$tmp_file="/tmp/hostname-$vm_uuid";
	
	$fp=fopen($tmp_file,'w');
	$tmp_netconf=<<<EOF
DEVICE=eth0
BOOTPROTO=static
ONBOOT=yes
IPADDR=$vm_ip_address
NETMASK=255.255.255.0
GATEWAY=$vm_gateway
EOF;
	fwrite($fp,$tmp_netconf);
	fclose($fp);

	run_ssh_key('localhost','root',"cat $tmp_file > /mnt/nbd$nbd_num/etc/sysconfig/network-scripts/ifcfg-eth0");

	$fp=fopen($tmp_file,'w');
	$tmp_netconf=<<<EOF
search @_DOMAIN_@
nameserver $dns_server
nameserver 168.126.63.1
nameserver 168.126.63.2
EOF;
	fwrite($fp,$tmp_netconf);
	fclose($fp);

	run_ssh_key('localhost','root',"cat $tmp_file > /mnt/nbd$nbd_num/etc/resolv.conf");

} else if ("$template_os_type" == "Debian") {

	## Networking 설정::Debian
	run_ssh_key('localhost','root',"echo $vm_name.@_DOMAIN_@ > /mnt/nbd$nbd_num/etc/hostname");
	
	## 임시: udev 삭제
#	run_ssh_key('localhost','root',"rm -f /etc/udev/rules.d/70-persistent-cd.rules /etc/udev/rules.d/70-persistent-net.rules /lib/udev/rules.d/75-net-description.rules /lib/udev/rules.d/75-persistent-net-generator.rules /lib/udev/rules.d/75-cd-aliases-generator.rules");

	## 임시: 여러가지...
	run_ssh_key('localhost','root',"sed -i '/172.21.19.116/d' /mnt/nbd$nbd_num/etc/apt/apt.conf /mnt/nbd$nbd_num/etc/wgetrc; sed -i '/172.21.80.54/d' /mnt/nbd$nbd_num/etc/apt/apt.conf /mnt/nbd$nbd_num/etc/wgetrc; sed -i 's/kr.archive.ubuntu.com/ftp.daum.net/g' /mnt/nbd$nbd_num/etc/apt/sources.list; sed -i '/^exit 0/d' /mnt/nbd$nbd_num/etc/rc.local; echo 'setterm -blank off' >> /mnt/nbd$nbd_num/etc/rc.local; echo 'resize2fs /dev/vda$part_num; sed -i \"/resize2fs/d\" /etc/rc.local' >> /mnt/nbd$nbd_num/etc/rc.local; echo 'apt-get update && apt-get -y install ntp && sed -i \"/apt-get -y install ntp/d\" /etc/rc.local' >> /mnt/nbd$nbd_num/etc/rc.local; echo 'exit 0' >> /mnt/nbd$nbd_num/etc/rc.local");

	$tmp_file="/tmp/hostname-$vm_uuid";

	$fp=fopen($tmp_file,'w');
	$tmp_netconf=<<<EOF
# The loopback network interface
auto lo
iface lo inet loopback

# The primary network interface
auto eth0
iface eth0 inet static
address $vm_ip_address
netmask 255.255.255.0
network $vm_network
broadcast $vm_broadcast
gateway $vm_gateway
EOF;
	fwrite($fp,$tmp_netconf);
	fclose($fp);

	run_ssh_key('localhost','root',"cat $tmp_file > /mnt/nbd$nbd_num/etc/network/interfaces");

	$fp=fopen($tmp_file,'w');
	$tmp_netconf=<<<EOF
search @_DOMAIN_@
nameserver $dns_server
nameserver 168.126.63.1
nameserver 168.126.63.2
EOF;
	fwrite($fp,$tmp_netconf);
	fclose($fp);

	run_ssh_key('localhost','root',"cat $tmp_file > /mnt/nbd$nbd_num/etc/resolv.conf; cat $tmp_file > /mnt/nbd$nbd_num/etc/resolvconf/resolv.conf.d/head");
} else if ("$template_os_type" == "vSwitch") {
	## Networking 설정::Debian
	#run_ssh_key('localhost','root',"echo $vm_name.@_DOMAIN_@ > /mnt/nbd$nbd_num/etc/hostname");
	
	## 임시: Porxy 서버 주소 변경
	#run_ssh_key('localhost','root',"sed -i 's/172.21.19.116/172.21.18.11/g' /mnt/nbd$nbd_num/etc/apt/apt.conf /mnt/nbd$nbd_num/etc/wgetrc");
	#run_ssh_key('localhost','root',"sed -i 's/172.21.80.54/172.21.18.11/g' /mnt/nbd$nbd_num/etc/apt/apt.conf /mnt/nbd$nbd_num/etc/wgetrc");
	#run_ssh_key('localhost','root',"sed -i '/172.21.18.11/d' /mnt/nbd$nbd_num/etc/apt/apt.conf /mnt/nbd$nbd_num/etc/wgetrc");
	#run_ssh_key('localhost','root',"sed -i '/^exit 0/d' /mnt/nbd$nbd_num/etc/rc.local;");
	#run_ssh_key('localhost','root',"echo 'apt-get update && apt-get -y install acpid' >> /mnt/nbd$nbd_num/etc/rc.local;");

	$tmp_file="/tmp/hostname-$vm_uuid";
	
	$ssh_key_pub_contents_tmp=explode(' ',file_get_contents("$path_sec/ssh-keypair/$loguser/$ssh_keypair_uuid.pub", true));
	$ssh_key_pub_contents=ereg_replace("/\n\r|\r\n/", "", $ssh_key_pub_contents_tmp[1]);
	
	$fp=fopen($tmp_file,'w');
	$tmp_netconf=<<<EOF
firewall {
    all-ping enable
    broadcast-ping disable
    ipv6-receive-redirects disable
    ipv6-src-route disable
    ip-src-route disable
    log-martians enable
    receive-redirects disable
    send-redirects enable
    source-validation disable
    syn-cookies enable
}
interfaces {
    ethernet eth0 {
	address $vm_ip_address/24
        duplex auto
        hw-id $vm_mac
        smp_affinity auto
        speed auto
    }
    ethernet eth1 {
	address 10.1.1.1/8
        duplex auto
        hw-id $vm_mac_2
        smp_affinity auto
        speed auto
    }
    loopback lo {
    }
}
nat {
    source {
        rule 1000 {
            description "MASQUERAE (eth0)"
            outbound-interface eth0
            translation {
                address masquerade
            }
        }
    }
}
service {
    ssh {
        disable-host-validation
        listen-address 0.0.0.0
        port 22
    }
}
system {
    config-management {
        commit-revisions 20
    }
    console {
        device ttyS0 {
            speed 9600
        }
    }
    gateway-address $vm_gateway
    host-name $vn_name
    name-server $dns_server
    name-server 168.126.63.2
    name-server 168.126.63.1
    ntp {
        server 0.vyatta.pool.ntp.org {
        }
        server 1.vyatta.pool.ntp.org {
        }
        server 2.vyatta.pool.ntp.org {
        }
    }
    package {
        auto-sync 1
        repository community {
            components main
            distribution stable
            password ""
            url http://packages.vyatta.com/vyatta
            username ""
        }
    }
    login {
        user vyatta {
            authentication {
                encrypted-password $1\$FHCR0sI5\$CD0Xj3t7PiDw1FPUwjeSR0
                plaintext-password ""
		public-keys ssh_key {
		    key $ssh_key_pub_contents
		    type ssh-rsa
		}
            }
            level admin
        }
    }
    syslog {
        global {
            facility all {
                level notice
            }
            facility protocols {
                level debug
            }
        }
    }
    time-zone Asia/Seoul
}


/* Warning: Do not remove the following line. */
/* === vyatta-config-version: "config-management@1:webproxy@1:dhcp-relay@1:webgui@1:firewall@5:ipsec@4:dhcp-server@4:zone-policy@1:qos@1:cluster@1:quagga@2:nat@4:wanloadbalance@3:conntrack@1:system@6:conntrack-sync@1:vrrp@1" === */
/* Release version: VC6.6R1 */
EOF;
	fwrite($fp,$tmp_netconf);
	fclose($fp);

	run_ssh_key('localhost','root',"cat $tmp_file > /mnt/nbd$nbd_num/opt/vyatta/etc/config/config.boot");
}

if ("$template_os_type" != "vSwitch") {
	## SSH Key 적재 및 used_count 1 증가
	run_ssh_key('localhost','root',"mkdir -p /mnt/nbd$nbd_num/root/.ssh; chmod 700 /mnt/nbd$nbd_num/root/.ssh");
	run_ssh_key('localhost','root',"cp $path_sec/ssh-keypair/$loguser/$ssh_keypair_uuid.pub /mnt/nbd$nbd_num/root/.ssh/authorized_keys; chmod 644 /mnt/nbd$nbd_num/root/.ssh/authorized_keys; chown root:root -R /mnt/nbd$nbd_num/root/.ssh");
}

# nwfilter 등록
run_ssh_key('localhost','root',"echo \"<filter name='vm-$vm_uuid' chain='root'><filterref filter='clean-traffic'/></filter>\"> $path_nwfilter_xml/vm-$vm_uuid.xml");
run_ssh_key($target_node,'root',"virsh nwfilter-define $path_nwfilter_xml/vm-$vm_uuid.xml");

$query_sshkey_count="select used_count from ssh_keypair where uuid='$ssh_keypair_uuid'";
$result_sshkey_count=@mysql_query($query_sshkey_count);
if (!$result_sshkey_count) {
	unmount_nbd($nbd_num);
	Query_Error();
}
$date_sshkey_count=@mysql_fetch_row($result_sshkey_count);
$sshkey_count_update=$date_sshkey_count['0']+1;
$query_sshkey_count_update="update ssh_keypair set used_count='$sshkey_count_update' where uuid='$ssh_keypair_uuid'";
$result_sshkey_count_update=@mysql_query($query_sshkey_count_update);
if (!$result_sshkey_count_update) {
	unmount_nbd($nbd_num);
	Query_Error();
}

$query_security_group_count="select used_count from security_group where uuid='$security_group_uuid'";
$result_security_group_count=@mysql_query($query_security_group_count);
if (!$result_security_group_count) {
	unmount_nbd($nbd_num);
	Query_Error();
}
$date_security_group_count=@mysql_fetch_row($result_security_group_count);
$security_group_count_update=$date_security_group_count['0']+1;
$query_security_group_count_update="update security_group set used_count='$security_group_count_update' where uuid='$security_group_uuid'";
$result_security_group_count_update=@mysql_query($query_security_group_count_update);
if (!$result_security_group_count_update) {
	unmount_nbd($nbd_num);
	Query_Error();
}

## Unmount Image
unmount_nbd($nbd_num);
run_ssh_key('localhost','root',"rm $tmp_file");
//echo "qemu-nbd -c /dev/nbd$nbd_num $path_instance/$vm_uuid; sleep 1; kpartx -a /dev/nbd$nbd_num; sleep 1; mount /dev/mapper/nbd${nbd_num}p2 /mnt/nbd$nbd_num<br>";
//echo "umount /mnt/nbd$nbd_num; sleep 1; kpartx -d /dev/nbd$nbd_num; sleep 1; qemu-nbd -d /dev/nbd$nbd_num<br>";
//exit;

if("$template_hypervisor"=="xen") {
#	run_ssh_key($target_node,'root',"if [ `/usr/sbin/brctl show | grep -c '^vlanbr$loguser_id'` -eq 0 ]; then /etc/xen/scripts/network-bridge-vlan vlan=$loguser_id start; fi > /dev/null");
#	run_ssh_key($target_node,'root',"if [ `ip address list dev eth0 | grep -c 10.1.1.1` -eq 0 ]; then /home/whitehole/set_ucarp_vlan.sh $target_node 10.1.1.1 $host_id $ucarp_pass; fi");
	$conn=libvirt_connect("xen+ssh://root@$target_node/","0");
	if ($data_volume==0) {
		$xml_str=<<<EOF
<domain type='xen'>
  <name>$vm_name</name>
  <uuid>$vm_uuid</uuid>
  <memory>$memory</memory>
  <currentMemory>$memory</currentMemory>
  <vcpu>$core</vcpu>
  <bootloader>/usr/bin/pygrub</bootloader>
  <on_poweroff>destroy</on_poweroff>
  <on_reboot>restart</on_reboot>
  <on_crash>restart</on_crash>
  <devices>
    <disk type='file' device='disk'>
      <driver name='tap' type='aio'/>
      <source file='$path_instance/$vm_uuid'/>
      <target dev='xvda' bus='xen'/>
    </disk>
    <interface type='bridge'>
      <alias name='eth00'/>
      <mac address='$vm_mac'/>
      <source bridge='br0'/>
      <filterref filter='vm-$vm_uuid'>
        <parameter name='IP' value='$vm_ip_address'/>
      </filterref>
      <script path='vif-bridge'/>
    </interface>
    <console type='pty' tty='/dev/pts/0'>
      <source path='/dev/pts/0'/>
      <target port='0'/>
    </console>
    <input type='mouse' bus='xen'/>
    <graphics type='vnc' autoport='yes' listen='127.0.0.1'/>
  </devices>
</domain>
EOF;
	} else {
		$xml_str=<<<EOF
<domain type='xen'>
  <name>$vm_name</name>
  <uuid>$vm_uuid</uuid>
  <memory>$memory</memory>
  <currentMemory>$memory</currentMemory>
  <vcpu>$core</vcpu>
  <bootloader>/usr/bin/pygrub</bootloader>
  <on_poweroff>destroy</on_poweroff>
  <on_reboot>restart</on_reboot>
  <on_crash>restart</on_crash>
  <devices>
    <disk type='file' device='disk'>
      <driver name='tap' type='aio'/>
      <source file='$path_instance/$vm_uuid'/>
      <target dev='xvda' bus='xen'/>
    </disk>
    <disk type='file' device='disk'>
      <driver name='tap' type='aio'/>
      <source file='$path_instance/$vm_uuid.data'/>
      <target dev='xvdb' bus='xen'/>
    </disk>
    <interface type='bridge'>
      <alias name='eth0'/>
      <mac address='$vm_mac'/>
      <filterref filter='vm-$vm_uuid'>
        <parameter name='IP' value='$vm_ip_address'/>
      </filterref>
      <source bridge='br0'/>
      <script path='vif-bridge'/>
    </interface>
    <console type='pty' tty='/dev/pts/0'>
      <source path='/dev/pts/0'/>
      <target port='0'/>
    </console>
    <input type='mouse' bus='xen'/>
    <graphics type='vnc' autoport='yes' listen='127.0.0.1'/>
  </devices>
</domain>
EOF;
	}
} else {
	$conn=libvirt_connect("qemu+ssh://root@$target_node/system","0");
	$kvm_bin="/usr/bin/kvm";
	if ("$template_bits"=="64") {
		$vm_arch="x86_64";
	} else {
		$vm_arch="i686";
	}
	if ("$template_os_type" != "vSwitch") {
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
    <bootmenu enable='no'/>
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
    <video>
      <model type='vga' vram='9216' heads='1'/>
      <alias name='video0'/>
    </video>
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
    <bootmenu enable='no'/>
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
    <video>
      <model type='vga' vram='9216' heads='1'/>
      <alias name='video0'/>
    </video>
    <input type='mouse' bus='ps2'/>
    <graphics type='vnc' autoport='yes' listen='127.0.0.1'/>
  </devices>
</domain>
EOF;
		}
	} else {
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
    <bootmenu enable='no'/>
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
    <interface type='bridge'>
      <mac address='$vm_mac'/>
      <source bridge='br0'/>
      <model type='virtio'/>
      <filterref filter='vm-$vm_uuid'>
        <parameter name='IP' value='$vm_ip_address'/>
      </filterref>
      <alias name='eth0'/>
    </interface>
    <interface type='bridge'>
      <mac address='$vm_mac_2'/>
      <source bridge='vsi42'/>
      <model type='virtio'/>
      <alias name='eth1'/>
    </interface>
    <console type='pty' tty='/dev/pts/0'>
      <source path='/dev/pts/0'/>
      <target port='0'/>
    </console>
    <video>
      <model type='vga' vram='9216' heads='1'/>
      <alias name='video0'/>
    </video>
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
    <bootmenu enable='no'/>
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
    <interface type='bridge'>
      <mac address='$vm_mac'/>
      <source bridge='br0'/>
      <model type='virtio'/>
      <filterref filter='vm-$vm_uuid'>
        <parameter name='IP' value='$vm_ip_address'/>
      </filterref>
      <alias name='eth0'/>
    </interface>
    <interface type='bridge'>
      <mac address='$vm_mac_2'/>
      <source bridge='vsi42'/>
      <model type='virtio'/>
      <alias name='eth1'/>
    </interface>
    <console type='pty' tty='/dev/pts/0'>
      <source path='/dev/pts/0'/>
      <target port='0'/>
    </console>
    <video>
      <model type='vga' vram='9216' heads='1'/>
      <alias name='video0'/>
    </video>
    <input type='mouse' bus='ps2'/>
    <graphics type='vnc' autoport='yes' listen='127.0.0.1'/>
  </devices>
</domain>
EOF;
		}
	}
}

# VM 생성

$resource=libvirt_domain_define_xml($conn, $xml_str);
$result_create=libvirt_domain_create($resource);
if ($result_create != "1") {
	$success=false;
}

# VM VNC 포트 확인
//$resource_vnc=libvirt_domain_lookup_by_uuid_string($conn,$vm_uuid);
//$return_vnc=explode(' ',strstr(libvirt_domain_get_xml_desc($resource_vnc,''),"type='vnc' port="));
//$vnc_port=preg_replace('/port=/','',preg_replace('\'','',$return_vnc['1']));

$dom=libvirt_domain_lookup_by_name($conn,"$vm_name");

$xml=libvirt_domain_get_xml_desc($dom,"");
$json=json_encode(new SimpleXMLElement($xml));
$xml_array=json_decode($json,TRUE);
$vnc_port=@$xml_array['devices']['graphics']['@attributes']['port'];

# MRTG 설정
run_ssh_key('localhost','root',"mkdir /var/www/mrtg/$vm_uuid; echo 'Preparing MRTG.......' > /var/www/mrtg/$vm_uuid/index.html; cp /home/whitehole/mrtg.template.cfg /home/whitehole/mrtg.cfg/$vm_uuid.cfg; sed -i 's/__uuid__/$vm_uuid/g' /home/whitehole/mrtg.cfg/$vm_uuid.cfg");

$query_create="insert into info_vm values ('$vm_uuid','$create_time','$ssh_keypair_uuid','$ssh_keypair_desc','$vm_ip_address','$vm_name','$core','$memory','$vm_mac','$template_bits','$template_hypervisor','$target_node','$vnc_port','$loguser','$data_volume','$template_os_type','$hostname','1','$security_group_uuid','T___$template_uuid','1','$root_volume')";

$result_create=@mysql_query($query_create);
if (!$result_create) {
	$success=false;
}

if (!$success) {
	$result_select_ip=@mysql_query("rollback");
	run_ssh_key($target_node,'root',"virsh nwfilter-undefine vm-$vm_uuid; rm -f $path_nwfilter_xml/vm-$vm_uuid.xml $path_instance/$vm_uuid* /var/www/mrtg/$vm_uuid /home/whitehole/mrtg.cfg/$vm_uuid.cfg; /home/whitehole/update-dns.sh delete $dns_server $vm_name.@_DOMAIN_@ $vm_ip_address");
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
			location.href=\"view_vm.php?pre_create=yes&pre_vm_uuid=$vm_uuid&pre_vm_name=$vm_name&pre_target_node=$target_node&pre_vnc_port=$vnc_port\"
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
