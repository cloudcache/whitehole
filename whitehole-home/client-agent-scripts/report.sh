#!/bin/bash

WORK_DIR="/home/whitehole"
#MYSQL_BIN="/usr/bin/mysql"
#DB_HOST="one-01"
#DB_USER="update_user"
#DB_PASS="16c913b1-4000-4b55-90cf-f1597570cce7"
#DB_NAME="whitehole"

#uuid=`cat $WORK_DIR/uuid.txt`
update_time=`date +%s`
#hostname=`hostname`
#ip_address=`/sbin/ip route | awk '/dev eth0  proto kernel  scope link  src/ {print $NF}'`
free_sys_cpu=`mpstat | awk '/all/ {print $11}'`
free_sys_mem=`free -m | awk '/buffers\/cache/ {print $NF}'`

#$MYSQL_BIN -u$DB_USER -p$DB_PASS -h$DB_HOST $DB_NAME -e "update info_node set update_time='$update_time',free_sys_cpu='$free_sys_cpu',free_sys_mem='$free_sys_mem' where uuid='$uuid' and hostname='$hostname' and ip_address='$ip_address'"

#echo $uuid
echo $update_time $free_sys_cpu $free_sys_mem
