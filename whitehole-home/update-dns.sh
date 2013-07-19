#!/bin/bash

NS_BIN="/usr/bin/nsupdate"

SERVER=$2
WORK_FQDN=$3
ZONE=`echo $WORK_FQDN | cut -d. -f2-`
WORK_IP=$4

function usage() {
    echo
    echo "  Usage : $0 {create|delete} {ACCOUNT} {IP}"
    echo
    exit 1
}

case "$1" in
    create)
${NS_BIN} << EOF
server ${SERVER}
zone ${ZONE}.
update add ${WORK_FQDN}. 60 IN A ${WORK_IP}
send
EOF
        ;;
    delete)
${NS_BIN} << EOF
server ${SERVER}
zone ${ZONE}
update delete ${WORK_FQDN} IN A ${WORK_IP}
send
EOF
        ;;
    *)
        usage
        ;;
esac

exit 0
