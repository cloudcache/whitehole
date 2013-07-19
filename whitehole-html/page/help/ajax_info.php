<?
include_once("_common.php");

switch ($type)
{
    case "stipulation":
        $text = $config[cf_stipulation];
        break;
    case "privacy":
        $text = $config[cf_privacy];
        break;
}

header("Content-Type: text/html; charset=$g4[charset]");
echo $text;
?>
