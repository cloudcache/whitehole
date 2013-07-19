<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "whitehole";
$Connect_DB = @mysql_connect($db_host,$db_user,$db_pass);
@mysql_select_db($db_name);

function alert_msg_close($error_message) {
	echo ("
		<script language=\"javascript\">
			alert(\"$error_message\");
			top.close();
		</script>
	");
}

function Query_Error() {
	$ErrorNumber = @mysql_errno();
	$ErrorString = @mysql_error();
	$ErrorNoString = "DB Error:" . $ErrorNumber . "-" . $ErrorString;
	alert_msg_close($ErrorNoString);
#	exit;
}

//$p_hosts = array('211.56.58.71','211.56.58.72','211.56.58.73','211.56.58.81','211.56.58.82','211.56.58.83','211.56.58.84','211.56.58.85','211.56.58.86','211.56.58.87');
$query="select ip_address from info_node where status='1'";
$result=mysql_query($query,$Connect_DB);
if (!$result) {
	Query_Error();
}
//mysql_close($db_conn);
//$fields=mysql_num_fields($result);
$f=0;
while ($row=mysql_fetch_row($result)) {
//	print_r($row);
//	echo ("$row[0]");
	$p_hosts[$f]=$row[0];
	$f++;
}
//print_r($p_hosts);

foreach ($p_hosts as $p_host) {
	$conn = libvirt_connect("qemu+ssh://root@".$p_host."/system");
	//echo $conn;

	if ($conn==false) {
		echo ("Libvirt last error: ".libvirt_get_last_error()."\n");
		exit;
	} else {
		$hostname=libvirt_connect_get_hostname($conn);
		$node_info=libvirt_node_get_info($conn);
		$domain_count=libvirt_domain_get_counts($conn);
		//$node_model=$node_info['model'];
		//$node_memory=$node_info['memory'];
		//$node_cpus=$node_info['cpus'];
		//$node_sockets=$node_info['sockets'];
		//$node_cores=$node_info['cores'];
		//$node_threads=$node_info['threads'];
		//$node_mhz=$node_info['mhz'];
		$node_active_vms=$domain_count['active'];
		$node_inactive_vms=$domain_count['inactive'];
		$node_total_vms=$domain_count['total'];

		$query="update info_node set active_vm_count='$node_active_vms',inactive_vm_count='$node_inactive_vms',total_vm_count='$node_total_vms' where ip_address='$p_host'";
		$result=mysql_query($query,$Connect_DB);
		if (!$result) {
			Query_Error();
		}

		$domains=libvirt_list_domains($conn);
		foreach ($domains as $dom) {
			$timestamp=mktime();
			//$date=date("Y-m-d H:i:s",$timestamp);
			$res=libvirt_domain_lookup_by_name($conn, $dom);

			$vm_name=libvirt_domain_get_name($res);
			$vm_uuid=libvirt_domain_get_uuid_string($res);
			$dominfo=libvirt_domain_get_info($res);
			$domblkstat_vda=@libvirt_domain_block_stats($res,"vda");
			$domblkstat_vdb=@libvirt_domain_block_stats($res,"vdb");
			//$domifstat=@libvirt_domain_interface_stats($res,"vnet0");

			//$res=libvirt_connect("qemu+ssh://root@172.21.81.140/system","0");
			//$dom=libvirt_domain_lookup_by_name($res,"TEST");
			$xml=libvirt_domain_get_xml_desc($res,"");
			$json=json_encode(new SimpleXMLElement($xml));
			$xml_array=json_decode($json,TRUE);
			$netdev=@$xml_array['devices']['interface']['target']['@attributes']['dev'];


			$domifstat=@libvirt_domain_interface_stats($res,$netdev);

			$vm_maxMem=$dominfo['maxMem'];
			$vm_memUsed=$dominfo['memory'];
			//$vm_state=$dominfo['state'];
			$vm_nrVirtCpu=$dominfo['nrVirtCpu'];
			$vm_cpuUsed=$dominfo['cpuUsed'];
			$vda_rd_req=$domblkstat_vda['rd_req'];
			$vda_rd_bytes=$domblkstat_vda['rd_bytes'];
			$vda_wr_req=$domblkstat_vda['wr_req'];
			$vda_wr_bytes=$domblkstat_vda['wr_bytes'];
			//$vda_errs=$domblkstat_vda['errs'];
			if (count($domblkstat_vdb)==1) {
				$domblkstat_vdb=array('rd_req'=>'0','rd_bytes'=>'0','wr_req'=>'0','wr_bytes'=>'0','errs'=>'0');
			}
			$vdb_rd_req=$domblkstat_vdb['rd_req'];
			$vdb_rd_bytes=$domblkstat_vdb['rd_bytes'];
			$vdb_wr_req=$domblkstat_vdb['wr_req'];
			$vdb_wr_bytes=$domblkstat_vdb['wr_bytes'];
			//$vdb_errs=$domblkstat_vdb['errs'];
			$vnet_rx_bytes=$domifstat['rx_bytes'];
			$vnet_rx_packets=$domifstat['rx_packets'];
			$vnet_rx_errs=$domifstat['rx_errs'];
			$vnet_rx_drop=$domifstat['rx_drop'];
			$vnet_tx_bytes=$domifstat['tx_bytes'];
			$vnet_tx_packets=$domifstat['tx_packets'];
			$vnet_tx_errs=$domifstat['tx_errs'];
			$vnet_tx_drop=$domifstat['tx_drop'];

			//$query="insert into monitoring values ('$timestamp','$vm_name','$vm_uuid','$vm_maxMem','$vm_memUsed','$vm_state','$vm_nrVirtCpu','$vm_cpuUsed','$vda_rd_req','$vda_rd_bytes','$vda_wr_req','$vda_wr_bytes','$vda_errs','$vdb_rd_req','$vdb_rd_bytes','$vdb_wr_req','$vdb_wr_bytes','$vdb_errs','$vnet_rx_bytes','$vnet_rx_packets','$vnet_rx_errs','$vnet_rx_drop','$vnet_tx_bytes','$vnet_tx_packets','$vnet_tx_errs','$vnet_tx_drop')";
			$query="insert into monitoring values ('','$timestamp','$vm_name','$vm_uuid','$vm_maxMem','$vm_memUsed','$vm_nrVirtCpu','$vm_cpuUsed','$vda_rd_req','$vda_rd_bytes','$vda_wr_req','$vda_wr_bytes','$vdb_rd_req','$vdb_rd_bytes','$vdb_wr_req','$vdb_wr_bytes','$vnet_rx_bytes','$vnet_rx_packets','$vnet_rx_errs','$vnet_rx_drop','$vnet_tx_bytes','$vnet_tx_packets','$vnet_tx_errs','$vnet_tx_drop')";
			$result=mysql_query($query,$Connect_DB);
			if (!$result) {
				Query_Error();
			}
		}
	}
}
?>
