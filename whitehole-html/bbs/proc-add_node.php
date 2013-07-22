<? include_once("./_head.whitehole.php"); ?>
<?
include "../db_conn.php";
include "../functions.php";

$ssh_host=$_POST['ip_address'];
$ssh_pass=$_POST['root_password'];
$hypervisor=$_POST['hypervisor'];

/*
echo $ssh_host;
echo $ssh_pass;
exit;
*/

//공통 pri,sec 스토리지 libvirt 자원으로 등록
$path_pri="/home/mnt/pri";
$path_sec="/home/mnt/sec";

#run_ssh_pass($ssh_host, 'root', $ssh_pass, 'yum -y install sysstat openssh-clients > /dev/null');
run_ssh_pass($ssh_host, 'root', $ssh_pass, "mkdir -p /root/.ssh $path_pri $path_sec");
run_ssh_pass($ssh_host, 'root', $ssh_pass, 'chmod 700 /root/.ssh');
run_scp_pass($ssh_host, 'root', $ssh_pass, '/var/www/.ssh/id_rsa.pub', "/root/.ssh/authorized_keys");
run_ssh_pass($ssh_host, 'root', $ssh_pass, 'chmod 0644 /root/.ssh/authorized_keys');
## WARNING: remote apt-get not works
#run_ssh_key($ssh_host, 'root', "sudo apt-get update && sudo apt-get -q -y install kvm libvirt-bin sysstat screen socat nfs-common");
$path_base="$path_pri/base";
run_ssh_key('localhost', 'root', "mkdir -p $path_base");
run_ssh_key($ssh_host, 'root', "echo 'options kvm_intel nested=1' > /etc/modprobe.d/kvm-nested_intel.conf; modprobe -r kvm_intel; modprobe kvm_intel");
run_ssh_key($ssh_host, 'root', "echo 'options kvm_amd nested=1' > /etc/modprobe.d/kvm-nested_amd.conf; modprobe -r kvm_amd; modprobe kvm_amd");
#run_scp_key($ssh_host, 'root', $ssh_pass, '/home/whitehole/client-agent-scripts/report.sh', "/home/whitehole/report.sh", 0700);
//run_scp_key($ssh_host, 'root', "/home/whitehole/client-agent-scripts/set_ucarp_vlan.sh", "/home/whitehole/set_ucarp_vlan.sh");
//run_ssh_key($ssh_host, 'root', "chmod 0700 /home/whitehole/set_ucarp_vlan.sh");
#run_ssh_key($ssh_host,'root',"echo '/sbin/ifconfig eth0 mtu 1412; /sbin/ethtool -K eth0 tx off; /sbin/iptables -t nat -A POSTROUTING -s 10.1.1.0/24 -d ! 10.1.1.0/24 -j SNAT --to $ssh_host' >> /etc/rc.local");
#run_ssh_key($ssh_host,'root',"echo '/sbin/ifconfig eth0 mtu 1412; /sbin/ethtool -K eth0 tx off; /sbin/iptables -t nat -A POSTROUTING -o eth0 -d ! 10.1.1.0/24 -j MASQUERADE' >> /etc/rc.local");
#run_ssh_key($ssh_host,'root',"echo '/sbin/ethtool -K eth0 tx off; /sbin/iptables -t nat -A POSTROUTING -o eth0 -d ! 10.1.1.0/24 -j MASQUERADE' >> /etc/rc.local; /sbin/ethtool -K eth0 tx off; /sbin/iptables -t nat -A POSTROUTING -o eth0 -d ! 10.1.1.0/24 -j MASQUERADE");
#run_ssh_key($ssh_host,'root',"echo '/sbin/ethtool -K eth0 tx off' >> /etc/rc.local; /sbin/ethtool -K eth0 tx off");

// Node Host-ID 결정
$query="select max(host_id) from info_node";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
$data=mysql_fetch_row($result);
$host_id=$data['0']+1;

// Primary 스토리지 정보 가져오기
$query="select uuid,host,fs_type,export_path from primary_storage";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
$data=mysql_fetch_row($result);
$pri_uuid=$data[0];
$pri_host=$data[1];
$fs_type_pri=$data[2];
$pri_export_path=$data[3];

// Secondary 스토리지 정보 가져오기
$query="select uuid,host,fs_type,export_path from secondary_storage";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
$data=mysql_fetch_row($result);
$sec_uuid=$data[0];
$sec_host=$data[1];
$fs_type_sec=$data[2];
$sec_export_path=$data[3];

#$conn=libvirt_connect("qemu+ssh://root@$ssh_host/system","0");
if("$hypervisor"=="xen") {
	$conn=libvirt_connect("xen+ssh://root@$ssh_host/","0");
} else {
	$conn=libvirt_connect("qemu+ssh://root@$ssh_host/system","0");
}

run_ssh_key($ssh_host,'root',"echo '$sec_host:$sec_export_path $path_sec $fs_type_sec defaults,_netdev 0 0' >> /etc/fstab");
run_ssh_key($ssh_host,'root',"/bin/mount -t $fs_type_sec -o defaults,_netdev $sec_host:$sec_export_path $path_sec");

run_ssh_key($ssh_host,'root',"echo '$pri_host:$pri_export_path $path_pri $fs_type_pri defaults,_netdev 0 0' >> /etc/fstab");
run_ssh_key($ssh_host,'root',"/bin/mount -t $fs_type_pri -o defaults,_netdev $sec_host:$pri_export_path $path_pri");

run_ssh_pass($ssh_host, 'root', $ssh_pass, "mkdir -p $path_pri/base $path_pri/instances $path_sec/iso $path_sec/ssh-keypair $path_sec/templates $path_sec/xml-nwfilter");

#run_ssh_key($ssh_host,'root',"/bin/mount -a");

#run_ssh_key($ssh_host,'root',"echo \"<filter name='e-iaas' chain='root'><filterref filter='clean-traffic'/></filter>\" > /etc/libvirt/e-iaas.xml");
#run_ssh_key($ssh_host,'root',"virsh nwfilter-define /etc/libvirt/e-iaas.xml");

#$resource_sec=libvirt_storagepool_define_xml($conn,"<pool type='netfs'><uuid>$sec_uuid</uuid><name>Secondary-Storage</name><source><host name='$sec_host'/><dir path='$sec_export_path'/><format type='$fs_type_sec'/></source><target><path>$path_sec</path></target></pool>");
$resource_sec=libvirt_storagepool_define_xml($conn,"<pool type='dir'><uuid>$sec_uuid</uuid><name>Secondary-Storage</name><target><path>$path_sec</path></target></pool>");
libvirt_storagepool_create($resource_sec);
libvirt_storagepool_set_autostart($resource_sec,'1');

#$resource_pri=libvirt_storagepool_define_xml($conn,"<pool type='netfs'><uuid>$pri_uuid</uuid><name>Primary-Storage</name><source><host name='$pri_host'/><dir path='$pri_export_path'/><format type='$fs_type_pri'/></source><target><path>$path_pri</path></target></pool>");
$resource_pri=libvirt_storagepool_define_xml($conn,"<pool type='dir'><uuid>$pri_uuid</uuid><name>Primary-Storage</name><target><path>$path_pri</path></target></pool>");
libvirt_storagepool_create($resource_pri);
libvirt_storagepool_set_autostart($resource_pri,'1');

$collect=rtrim(run_ssh_key($ssh_host,'root',"(date +%s; mpstat | awk '/all/ {print \$11}'; free -m | awk '/buffers\/cache/ {print \$NF}') | tr '\n' ' '"));
$return=explode(' ',$collect);
$update_time=$return['0'];
$free_sys_cpu=$return['1'];
$free_sys_mem=$return['2']-256;

//run_ssh_key($ssh_host,'root','rpm -Uvh http://download.fedora.redhat.com/pub/epel/5/x86_64/epel-release-5-4.noarch.rpm; yum -y install qemu-img qemu-common kpartx');
$hostname=rtrim(run_ssh_key($ssh_host, 'root', "hostname"));
$total_sys_memory=run_ssh_key($ssh_host, 'root', "free -m | awk '/Mem:/ {print \$2}'");
#$total_sys_memory=run_ssh_key($ssh_host, 'root', "xentop -b -i 1 | awk '/Mem:/ {print int(\$2/1024)}'");
$total_sys_core=rtrim(run_ssh_key($ssh_host, 'root', "grep -c 'processor' /proc/cpuinfo"));
#$total_sys_core=run_ssh_key($ssh_host, 'root', "xentop -b -i 1 | awk '/Mem:/ {print \$9}");
$create_time=time();

//echo $total_memory;
//echo $total_core;

$query="insert into info_node value ('$ssh_host','$hostname','$create_time','$update_time','1','$total_sys_core','$total_sys_memory','$free_sys_cpu','$free_sys_mem','','','','$hypervisor','$host_id')";
$result=@mysql_query($query);
if (!$result) {
	Query_Error();
}
echo ("
	<script language=\"javascript\">
		location.href=\"view_nodes.php\"
	</script>
");
?>
