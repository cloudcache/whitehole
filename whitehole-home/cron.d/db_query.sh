#!/bin/bash

uuid=$1
m1=$2
m2=$3

db_host="localhost"
db_user="root"
db_pass="1234"
db_name="whitehole"

mysql_cmd="/usr/bin/mysql -u$db_user -p$db_pass -h $db_host $db_name -e"

result=(`$mysql_cmd "select $m1,$m2 from monitoring where uuid='$uuid' order by timestamp desc limit 1" | tail -n 1`)

echo ${result[0]}
echo ${result[1]}
