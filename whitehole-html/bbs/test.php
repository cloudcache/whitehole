<?

include "../db_conn.php";
include "../functions.php";

print_r(run_ssh_key('localhost','root',"ls -al /home"));
