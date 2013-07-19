<?
/**
 * Bechu basic skin for gnuboard4
 *
 * copyright (c) 2008 Choi Jae-Young <www.miwit.com>
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

include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if ($is_admin != "super")
    die("접근 권한이 없습니다.");

// 환경설정 파일 경로
$mw_basic_config_path = "$g4[path]/data/mw.basic.config";
$mw_basic_config_file = "$mw_basic_config_path/$bo_table";
mw_mkdir($mw_basic_config_path, 0707);

include_once("$board_skin_path/mw.adm/mw.upgrade.php");
$mw_basic_upgrade_time_file = "$mw_basic_config_path/{$bo_table}_upgrade_time";
mw_write_file($mw_basic_upgrade_time_file, filectime("$board_skin_path/mw.adm/mw.upgrade.php"));

// 환경설정  파일 없으면 생성
mw_basic_write_config_file();

die("설정다시읽기 완료");

