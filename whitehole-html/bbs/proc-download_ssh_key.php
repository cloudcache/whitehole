<?

$key_path=$_GET['key_path'];
$key_file=$_GET['key_file'];

$full_path="$key_path/$key_file";
//echo $full_path; exit;

$mm_type="application/octet-stream";

header("Content-Type: " . $mm_type);
header('Content-Disposition: attachment; filename="'.$key_file.'"');
                  
readfile($full_path);

?>
