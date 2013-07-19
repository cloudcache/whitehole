#!/bin/bash

## Usage
# ./setup_vlan_ucarp.sh [Source-IP] [V-IP] [VLAN-ID] [HOST-ID] [PASS]

# Source function library.
#. /etc/init.d/functions

SRC_IP=$1
VIP=$2
HOST_ID=$3
PASS=$4

PID=$$
PID_FILE="/var/run/ucarp-br0.pid"

/usr/sbin/ucarp --daemonize --interface=eth0 --pass=$PASS --srcip=$SRC_IP --vhid=$HOST_ID --addr=$VIP --shutdown --preempt --upscript=/usr/libexec/ucarp/vip-up --downscript=/usr/libexec/ucarp/vip-down

if [ $? -eq 0 ]; then
    echo $PID >> $PID_FILE
fi
