<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

// 사용자 화면 우측과 하단을 담당하는 페이지입니다.
// 우측, 하단 화면을 꾸미려면 이 파일을 수정합니다.
?>
    </td>
</tr>
</table>

<?/* sitemap 필요하신분은 주석 제거후 사용하세요 
<style type="text/css">
#tail { margin:5px 0 0 0; border:1px solid #dedede; background-color:#f4f4f4; }
#tail .sitemap { margin:5px; padding:10px 0 0 10px; background-color:#fff; border:1px solid #fff; letter-spacing:0px; }
#tail .sitemap ul { margin:0; padding:0; list-style:none; height:25px; }
#tail .sitemap ul li { margin:0; padding:0; float:left; }
#tail .sitemap ul li .group { font-weight:bold; padding:0 0 0 10px; float:left; width:80px; } 
#tail .sitemap ul li .group a { color:#5695D4; }
#tail .sitemap ul li .menu { margin-left:1px; padding:0 0 0 10px; background:url(<?=$mw_index_skin_main_path?>/img/dot.gif) 3px 5px no-repeat; }
#tail .sl { float:left; }
#tail .sitemap .gag { clear:both; height:1px; line-height:1px; font-size:1px; }
</style>

<div id="tail">
<div class="sitemap">
<?
$sql = "select gr_id, gr_subject from $g4[group_table] ";
$qry = sql_query($sql);
for ($i=0; $row=sql_fetch_array($qry); $i++) {
    echo "<ul $sline>\n";
    echo "<li><div class=\"group\"><a href=\"{$g4[bbs_path]}/group.php?gr_id={$row[gr_id]}\">{$row[gr_subject]}</a></div></li>\n";
    $sql2 = "select bo_table, bo_subject from $g4[board_table] where gr_id = '$row[gr_id]' order by bo_order_search";
    $qry2 = sql_query($sql2);
    for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
	echo "<li><div class=\"menu\"><a href=\"{$g4[bbs_path]}/board.php?bo_table={$row2[bo_table]}\">{$row2[bo_subject]}</a></div></li>\n";
    }
    echo "</ul>\n";
    if (($i+1)%6==0) echo "<div class=gag>&nbsp;</div>";
}
?>
<div class="gag"></div>
</div><!-- sitemap -->
</div><!-- tail -->
*/
?>



<style type="text/css">
#mw-site-info { border-top:1px solid #ddd; }  
#mw-site-info { clear:both; text-align:center; margin:10px 0 20px 0; padding:10px; color:#555; font-size:8pt; }
#mw-site-info .mw-banner { height:30px; margin:0 0 10px 0; text-align:center; }
#mw-site-info .mw-banner span { margin:0 5px 0 5px; }
#mw-site-info .menu { color:#ddd; line-height:25px; }
#mw-site-info .menu a { color:#555;  }
#mw-site-info .d { color:#ddd; margin:0 2px 0 2px; }
#mw-site-info a.site { color:#3173B6;  }
#mw-site-info a:hover { text-decoration:underline; }
#mw-site-info .copyright { margin:0 0 10px 0; }
</style>

<div id="mw-site-info">
<!--
    <div class="mw-banner">
        <span><a href="http://www.miwit.com" target=_blank><img src="<?=$g4[path]?>/img/b1.gif" alt="miwit.com"></a></span>
        <span><a href="http://www.sir.co.kr" target=_blank><img src="<?=$g4[path]?>/img/b2.gif" alt="sir.co.kr"></a></span>
        <span><a href="http://www.dnsever.com" target="_blank"><img src="<?=$g4[path]?>/img/b3.gif" alt="DNS Powered by DNSEver.com"></a></span>
        <span><a href="#" target=_blank><img src="<?=$g4[path]?>/img/banner-tail.gif"></a></span>
        <span><a href="#" target=_blank><img src="<?=$g4[path]?>/img/banner-tail.gif"></a></span>
    </div>
    <div class="menu">
        <a href="#">회사소개</a>
        <span class="d">|</span> <a href="<?=$g4[path]?>/page/help/stipulation.php">이용약관</a>
        <span class="d">|</span> <a href="<?=$g4[path]?>/page/help/privacy.php">개인정보취급방침</a>
        <span class="d">|</span> <a href="<?=$g4[path]?>/page/help/disclaimer.php">책임의한계와 법적고지</a>
        <span class="d">|</span> <a href="<?=$g4[path]?>/page/help/rejection.php">이메일무단수집거부</a>
        <span class="d">|</span> <a href="<?=$g4[bbs_path]?>/board.php?bo_table=notice">이용안내</a>
    </div>
    <div class="copyright">Copyright ⓒ <a href="<?=$g4[url]?>" class="site"><?=$g4[url]?></a>.  All rights reserved.</div>
</div>
-->

</div> <!-- #mw-index -->

<?
include_once("$g4[path]/tail.sub.php");
