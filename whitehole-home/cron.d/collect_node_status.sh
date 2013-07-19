#!/bin/bash

MYSQL_BIN="/usr/bin/mysql"
DB_HOST="localhost"
DB_USER="root"
DB_PASS="1234"
DB_NAME="whitehole"


HOSTS=`$MYSQL_BIN -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e "select ip_address from info_node where status='1'" | grep -v ip_address`

for host in $HOSTS
do
	result=(`ssh root@$host /home/whitehole/report.sh 2> /dev/null`)

	update_time=${result[0]}
	free_sys_cpu=${result[1]}
	free_sys_mem=${result[2]}

	#echo $update_time
	#echo $free_sys_cpu
	#echo $free_sys_mem

	$MYSQL_BIN -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e "update info_node set update_time='$update_time',free_sys_cpu='$free_sys_cpu',free_sys_mem='$free_sys_mem' where ip_address='$host'"
done
