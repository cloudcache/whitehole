<?
		$xml="<filter name='vm-$vm_uuid'>";
		$xml.="<filterref filter='clean-traffic'/>";
		$xml.="<rule action='accept' direction='in'><all state='ESTABLISHED,RELATED'/></rule>";
		$xml.="<rule action='accept' direction='out'><all state='NEW,ESTABLISHED,RELATED'/></rule>";

		$query_sg="select * from security_ruleset where uuid='$security_group_uuid'";
		$result_sg=@mysql_query($query_sg);
		if (mysql_num_rows($result_sg)>0) {
			while ($data_sg=mysql_fetch_row($result_sg)) {
				$num=$data_sg['0'];
				$create_time=$data_sg['1'];
				$uuid=$data_sg['2'];
				$rule_name=$data_sg['3'];
				$account=$data_sg['4'];
				$protocol=$data_sg['5'];
				$src_ip=$data_sg['6'];
				$src_ip_mask=$data_sg['7'];
				$dst_port_start=$data_sg['8'];
				$dst_port_end=$data_sg['9'];
				$action=$data_sg['10'];
				if ($protocol=="icmp") {
					$xml.="<rule action='$action' direction='in'><$protocol srcipaddr='$src_ip' srcipmask='$src_ip_mask' state='NEW'/></rule>";
				} else {
					$xml.="<rule action='$action' direction='in'><$protocol srcipaddr='$src_ip' srcipmask='$src_ip_mask' dstportstart='$dst_port_start' dstportend='$dst_port_end' state='NEW'/></rule>";
				}
			}
		}
		$xml.="<rule action='drop' direction='in'><all/></rule>";
		$xml.="</filter>";

		run_ssh_key('localhost','root',"echo \"$xml\" > $path_nwfilter_xml/vm-$vm_uuid.xml");
		run_ssh_key($target_node,'root',"virsh nwfilter-define $path_nwfilter_xml/vm-$vm_uuid.xml");
?>
