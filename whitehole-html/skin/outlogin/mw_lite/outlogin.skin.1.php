<?
/**
 * Bechu-Outlogin Skin for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if ($g4['https_url']) {
    $outlogin_url = $_GET['url'];
    if ($outlogin_url) {
        if (preg_match("/^\.\.\//", $outlogin_url)) {
            $outlogin_url = urlencode($g4[url]."/".preg_replace("/^\.\.\//", "", $outlogin_url));
        }
        else {
            $purl = parse_url($g4[url]);
            if ($purl[path]) {
                $path = urlencode($purl[path]);
                $urlencode = preg_replace("/".$path."/", "", $urlencode);
            }
            $outlogin_url = $g4[url].$urlencode;
        }
    }
    else {
        $outlogin_url = $g4[url];
    }
}
else {
    $outlogin_url = $urlencode;
}
?>

<!-- 로그인 전 외부로그인 시작 -->
<div id="mw-outlogin">
<form name="foutlogin" method="post" onsubmit="return foutlogin_submit(document.foutlogin);" autocomplete="off">
<input type="hidden" name="url" value="<?=$outlogin_url?>">
<div class="box-outside">
<div class="box-inside">
    <div class="login-title"><strong><?=$config[cf_title]?></strong> 로그인</div>
    <input type="text" name="mb_id" id="mb_id" class="login-mb_id" value="아이디">
    <input type="password" name="mb_password" id="mb_password" class="login-mb_password" value="">
    <input type="image"  class="login-button" src="<?=$outlogin_skin_path?>/img/outlogin_button.gif" align="absmiddle">
    <div class="login-auto"><input type="checkbox" name="auto_login" id="auto_login">자동</div>
    <div class="login-membership">
        <a href="<?=$g4[bbs_path]?>/register.php"><strong>회원가입</strong></a> <span>|</span>
        <a href="javascript:win_password_lost();">아이디·비밀번호찾기</a>
    </div>
</div>
</div>
</form>
</div>
<!-- 로그인 전 외부로그인 끝 -->

<script type="text/javascript">
document.getElementById("mb_id").onfocus = function() { mw_outlogin_focus_id(this); }
document.getElementById("mb_id").onblur = function() { mw_outlogin_blur_id(this); }
document.getElementById("mb_password").onfocus = function() { mw_outlogin_focus_pw(this); }
document.getElementById("mb_password").onblur = function() { mw_outlogin_blur_pw(this); }
document.getElementById("mb_password").onblur = function() { mw_outlogin_blur_pw(this); }
document.getElementById("auto_login").onclick = function() { mw_outlogin_auto(this); }

function mw_outlogin_focus_id(obj) {
    if (obj.value == "아이디") {
        obj.value = "";
    }
    //obj.style.border = "1px solid #7dacd8";
    obj.style.border = "1px solid #777";
}
function mw_outlogin_blur_id(obj) {
    if (obj.value == "") {
        obj.value = "아이디";
    }
    obj.style.border = "1px solid #ccc";
}
function mw_outlogin_focus_pw(obj) {
    if (obj.value == "") {
        obj.style.background = "#fff";
    }
    //obj.style.border = "1px solid #7dacd8";
    obj.style.border = "1px solid #777";
}
function mw_outlogin_blur_pw(obj) {
    if (obj.value == "") {
        obj.style.background = "url(<?=$outlogin_skin_path?>/img/outlogin_pw.gif) no-repeat";
    }
    obj.style.border = "1px solid #ccc";
}
function mw_outlogin_auto(obj) {
    if (obj.checked) {
        if (confirm("자동로그인을 사용하시면 다음부터 회원아이디와 패스워드를 입력하실 필요가 없습니다.\n\n\공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?")) {
            obj.checked = true;
        } else {
            obj.checked = false;
        }
    }
}
function foutlogin_submit(f)
{
    if (!f.mb_id.value || f.mb_id.value=='아이디') {
        alert("회원아이디를 입력하십시오.");
        f.mb_id.focus();
        return false;
    }
    if (!f.mb_password.value) {
        alert("패스워드를 입력하십시오.");
        f.mb_password.focus();
        return false;
    }

    <?
    if ($g4[https_url])
        echo "f.action = '$g4[https_url]/$g4[bbs]/login_check.php';";
    else
        echo "f.action = '$g4[bbs_path]/login_check.php';";
    ?>
    f.submit();
}
</script>

<style type="text/css">
#mw-outlogin .box-outside { width:180px; height:100px; background-color:#d0e1f1; }
#mw-outlogin .box-inside { position:absolute; margin:2px; width:176px; height:96px; background-color:#f3f9fc; }
#mw-outlogin .box-inside { line-height:16px; color:#7dacd8; font-size:9pt; font-family:gulim; }
#mw-outlogin .login-title { position:absolute; margin:5px 0 0 7px; }
#mw-outlogin .login-mb_id { position:absolute; margin:25px 0 0 7px; padding:4px 0 0 3px; border:1px solid #d0e1f1; width:100px; height:20px; }
#mw-outlogin .login-mb_id { font:normal 11px 'gulim'; color:#7dacd8; ime-mode:disabled; }
#mw-outlogin .login-mb_password { position:absolute; margin:48px 0 0 7px; padding:3px 0 0 2px; border:1px solid #d0e1f1; }
#mw-outlogin .login-mb_password { width:100px; height:20px; font-size:8pt; color:#7dacd8; background:url(<?=$outlogin_skin_path?>/img/outlogin_pw.gif) no-repeat; }
#mw-outlogin .login-button { position:absolute; margin:48px 0 0 113px; }
#mw-outlogin .login-auto { position:absolute; margin:25px 0 0 113px; font-size:8pt; }
#mw-outlogin .login-membership { position:absolute; margin:74px 0 0 7px; padding:3px 0 0 0; border-top:1px solid #d0e1f1; width:162px; }
#mw-outlogin .login-membership { text-align:center; font-size:8pt; color:#d0e1f1; }
#mw-outlogin .login-membership a { color:#7dacd8; font-size:8pt; text-decoration:none; }

#mw-outlogin .box-outside { background-color:#e1e1e1; }
#mw-outlogin .box-inside { background-color:#fafafa; color:#777; }
#mw-outlogin .login-mb_id { border:1px solid #ccc; color:#444; }
#mw-outlogin .login-mb_password { border:1px solid #ccc; color:#444; }
#mw-outlogin .login-membership { color:#777; border-top:1px solid #e1e1e1; }
#mw-outlogin .login-membership a { color:#777; }

</style>

