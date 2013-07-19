#!/bin/bash

cfg_dir="/home/whitehole/mrtg.cfg"
cfg_list=`find $cfg_dir -type f -name \*.cfg`
mrtg="/usr/bin/mrtg"
indexmaker="/usr/bin/indexmaker"
run_log_dir="/var/log/mrtg"

for cfg_file in $cfg_list
do
        work_dir=`grep "^WorkDir: " $cfg_file | awk '{print $2}'`
        uuid=`basename $cfg_file | awk -F'.cfg' '{print $1}'`
        if [ ! -d $work_dir ]; then mkdir -p $work_dir; fi
        #if [ ! -f $work_dir/index.html ]; then $indexmaker --title=$uuid --output=$work_dir/index.html $cfg_file; fi
        $indexmaker --title=$uuid --output=$work_dir/index.html $cfg_file
        env LANG=C $mrtg $cfg_file --logging $run_log_dir/$uuid.log
done
