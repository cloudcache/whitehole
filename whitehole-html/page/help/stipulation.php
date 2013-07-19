<?php
include_once("_common.php");
include_once("_head.php");
?>

<style type="text/css">
h3 { font-size:15px; font-weight:bold; text-align:left; }
.info { width:100%; height:500px; border:1px solid #ddd; padding:10px; font-size:13px; line-height:18px; font-family:gulim; }
</style>

<h3> 이용약관 </h3>
<textarea id="cf_stipulation" class="info" readonly></textarea>

<script type="text/javascript">
$.get("ajax_info.php?type=stipulation", function (req) {
    $("#cf_stipulation").val(req);
});

$(document).bind("contextmenu", function(e){
    alert("퍼가지 마세요.\n\n퍼가더라도 회사명 꼭 변경 바랍니다.");
    return false;
});
</script>

<?
include_once("_tail.php");
