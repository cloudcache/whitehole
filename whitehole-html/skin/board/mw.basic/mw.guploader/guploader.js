guploader = function(gup_name) {

// 업로더 이름
var gup_name = gup_name;

// 업로더 가로 사이즈
var gup_width = '100%';

// 업로더 경로 (guploader.js 파일의 경로)
var gup_path = '';

var gup_all_size_limit   = 0;
var gup_all_size_used    = 0;
var gup_upload_size      = 0;

var gup_file_list_limit  = 0;
var gup_file_size_limit  = 0;
var gup_file_path        = '';
var gup_file_action      = '';
var gup_uploader         = '';
var gup_after_upload     = '';
var gup_delete_file      = '';
var gup_download_file    = '';
var gup_after_upload_val = '';
var gup_image_width     = 0;

var gup_anti_ext = ["php","phtm","htm","html","cgi","pl","exe","jsp","asp","inc"];
var fl_width = 0;

this.set_path = function (gpath) {
    if (!gup_path) gup_path = gpath; else this._error(); 
}

this.set_image_width = function(image_width) {
    if (!gup_image_width) gup_image_width = parseInt(image_width); else this._error();
}

this.set_all_size_limit = function(all_size_limit) {
    if (!gup_all_size_limit) gup_all_size_limit = parseInt(all_size_limit); else this._error();
}

this.set_all_size_used = function(all_size_used) {
    if (!gup_all_size_used) gup_all_size_used = parseInt(all_size_used); else this._error();
}

this.set_file_list_limit = function(file_list_limit) {
    if (!gup_file_list_limit) gup_file_list_limit = parseInt(file_list_limit); else this._error();
}

this.set_file_size_limit = function(file_size_limit) {
    if (!gup_file_size_limit) gup_file_size_limit = parseInt(file_size_limit); else this._error();
}

this.set_file_path = function(file_path) {
    if (!gup_file_path) gup_file_path = file_path; else this._error();
}

this.set_file_action = function(file_action) {
    if (!gup_file_action) gup_file_action = file_action; else this._error();
}

this.set_after_upload = function(after_upload) {
    if (!gup_after_upload) gup_after_upload = after_upload; else this._error();
}

this.set_after_upload_val = function(val) {
    if (!gup_after_upload_val) gup_after_upload_val = val; else this._error();
}

this.set_delete_file = function(delete_file) {
    if (!gup_delete_file) gup_delete_file = delete_file; else this._error();
}

this.set_download_file = function(filename) {
    if (!gup_download_file) gup_download_file = filename; else this._error();
}

this.draw_uploader = function() {
    draw  = "<table border=0 cellpadding=0 cellspacing=0 width="+gup_width+" height=180><tr>";
    draw += "<td width=190 id="+gup_name+"_image_preview valign=center align=center style=\"background-color:#efefef; border:1px solid #ccc; text-align:center; font-size:12px;\">미리보기</td><td width=10></td>";
    draw += "<td><select name="+gup_name+"_files_list id="+gup_name+"_files_list style=\"width:100%;overflow:hidden;\" size=6 onchange=\""+gup_name+".preview()\"></select>";
    draw += "<table border=0 cellpadding=0 cellspacing=0 width=100% height=30 style=\"margin-top:5px;margin-bottom:0px;\"><tr><td>";
    draw += "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"";
    draw += "id=\""+gup_name+"_flash_uploader\" width=\"100%\" height=\"80\" style=\"z-index:1;\"";
    draw += "codebase=\"http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab\">";
    draw += "<param name=\"movie\" value=\""+gup_path+"/guploader.swf\" />";
    draw += "<param name=\"quality\" value=\"high\" />";
    draw += "<param name=\"bgcolor\" value=\"#ffffff\" />";
    draw += "<param name=\"wmode\" value=\"transparent\" />";    
    draw += "<param name=\"allowScriptAccess\" value=\"sameDomain\" />";
    draw += "<embed src=\""+gup_path+"/guploader.swf\" quality=\"high\" bgcolor=\"#ffffff\" wmode=\"transparent\"";
    draw += "width=\"100%\" height=\"80\" name=\""+gup_name+"_flash_uploader\" align=\"middle\"";
    draw += "play=\"true\"";
    draw += "loop=\"false\"";
    draw += "quality=\"high\"";
    draw += "allowScriptAccess=\"sameDomain\"";
    draw += "type=\"application/x-shockwave-flash\"";
    draw += "pluginspage=\"http://www.adobe.com/go/getflashplayer\">";
    draw += "</embed></object>";
    draw += "</td></tr></table></td></tr><tr><td></td><td></td><td height=40 align=right>";
    draw += "&nbsp;&nbsp;";
    draw += "</td></tr></table>";
    document.write(draw);

    gup_uploader = eval("window.document."+gup_name+"_flash_uploader");

    this.flex_js(gup_name+"_flash_uploader");

    this.after_upload();
}

// 삐꺽삐꺽님 지업로더 플레시 플레이어 10 수정 팁
// http://www.sir.co.kr/bbs/board.php?bo_table=g4_tiptech&wr_id=17955
this.browse_open_val = function()
{
    var ret = {};
    ret.file_list_length = document.getElementById(gup_name+"_files_list").options.length;
    ret.gup_file_list_limit = gup_file_list_limit;
    ret.gup_file_size_limit = gup_file_size_limit;
    ret.gup_name = gup_name;
    ret.gup_file_action = gup_file_action;
    
    return ret;
}

/*this.browse_open = function() {
    if (gup_file_list_limit && document.getElementById(gup_name+"_files_list").options.length >= gup_file_list_limit) {
        alert('더이상 업로드 하실 수 없습니다.'); 
        return false;
    }
    gup_uploader.list_limit(gup_file_list_limit);
    gup_uploader.size_limit(gup_file_size_limit);
    gup_uploader.select(gup_name + ".file_select" );
    gup_uploader.path(gup_file_action);
    gup_uploader.after_upload(gup_name + ".after_delay");
    gup_uploader.browse();
}*/

this.file_select = function() {
    var list = gup_uploader.get_list();
    var fl = document.getElementById(gup_name+"_files_list");

    if (gup_file_list_limit && (fl.length + list.length) > gup_file_list_limit) {
        alert(gup_file_list_limit+'개만 업로드 하실 수 있습니다.'); 
        return false;
    }

    var size = gup_uploader.get_size();
    var tmp_size = 0;

    for (i=0; i<size.length; i++) {
        file = list[i];
        ext = this.get_ext(file);
        for (j=0; j<gup_anti_ext.length; j++) {
            if (ext == gup_anti_ext[j]) {
                alert('확장자 '+gup_anti_ext+' 파일은 업로드 하실 수 없습니다.');
                return false;
            }
        }
        for (j=0; j<fl.options.length; j++) {
            if (file==fl.options[j].value) {
                alert('같은이름의 파일을 이미 업로드하셨습니다.');
                return false;
            }
        }
        tmp_size += parseInt(size[i]);
    }

    if (gup_all_size_limit && (gup_all_size_used + tmp_size) > gup_all_size_limit) {
        alert('허용된 총 용량을 초과하였습니다.');
        return false;
    }

    var msg = gup_uploader.send();

    switch(msg) {
        case "false": 
            break;
        case "true": 
            //document.getElementById(gup_name+"_btn_upload").disabled = true;
            break;
        default: 
            alert(msg); 
            break;
    }
}

this.file_to_editor = function() {
    var files_list = document.getElementById(gup_name+"_files_list");
    var html = '';

    if (!files_list.value) {
        alert('파일을 선택해주세요.'); 
        return false;
    }

    for (i=0; i<files_list.options.length; i++) {

        if (files_list.options[i].selected == true) {

            file = this.get_file_info(files_list.options[i].value);
            html = "{이미지:" + i + "}";

            /*if (gup_download_file) {
                path = gup_download_file + '&id=' + file.id;
            } else {
                path = gup_file_path + '/' + file.save_name;
            }
            ext = this.get_ext(file.real_name);

            if (ext=='jpg' || ext=='gif' || ext=='png')  {
                path = gup_file_path + '/' + file.save_name;
                var size = this.get_image_size(path);

                if (size[0] > gup_image_width) 
                    width = gup_image_width; 
                else 
                    width = size[0];

                html += "<img src=\"" + path + "\"";
                if(width)  
                    html += "width=" + width;
                html += "><br/>\n";

            } else {
                html += "<a href=\"" + path + "\">" + file.real_name + "</a><br/>\n";
            }*/
        }
    }

    this.insert_editor(html);
}

this.preview = function() {
    var file = this.get_file_info(document.getElementById(gup_name+"_files_list").value);
    var src = gup_file_path + '/' + file.save_name;
    var img = "<img src=\"" + src + "\" width=150 onerror=\""+gup_name+".preview_error()\">";
    document.getElementById(gup_name+"_image_preview").innerHTML = img;
}


this.preview_error = function() {
    document.getElementById(gup_name+"_image_preview").innerHTML = "미리보기";
}

this.insert_editor = function(html) {
    //geditor_content.get_range();
    //geditor_content.insert_editor(html);
    if(typeof geditor_wr_content != "undefined") {
        if (geditor_wr_content.get_mode() == "WYSIWYG") {
            document.getElementById("geditor_wr_content_frame").contentWindow.document.body.focus();
            geditor_wr_content.get_range();
            html = "<br/>" + html;
        }
        else if (geditor_wr_content.get_mode() == "TEXT") {
            html = "\n" + html;
        }
        else {
            html = "<br/>" + html;
        }
        geditor_wr_content.insert_editor(html);
    } else {
        document.getElementById("wr_content").value += "\n" + html;
    }
}

this.after_delay = function() {
    setTimeout(gup_after_upload+'()',1000);
}

this.after_upload = function() {
    var fl = document.getElementById(gup_name+"_files_list");
    var url = gup_path + "/guploader_list.php";
    var param = gup_after_upload_val;
    //document.getElementById(gup_name+"_btn_upload").disabled = false;
    //var myAjax = new Ajax.Request(
    $.ajax({
        type: 'post',
        url: url,
        data: param,
        success : after_upload_get_file_info = function(req) {
                fl.options.length = 0;
                var json = eval('('+req+')');
                var tt = '';
                for (var i=0; i<json.files.length; i++) {
                    option = document.createElement("option");
                    option.innerHTML = json.files[i].real_name + "  (" + eval(gup_name + ".get_size_type(json.files[i].file_size)") + ")";
                    option.value = json.files[i].id + '|' + json.files[i].file_num + '|' + json.files[i].save_name + '|'
                                 + json.files[i].real_name + '|' + json.files[i].file_size;
                    option.style.overflow = 'hidden';
                    fl.appendChild(option);
                    tt += json.files[i].real_name + "\n";
                }
            }
        });
}

this.flex_js = function(movie_name) {
    if(!window.fakeMovies) 
        window.fakeMovies = new Array();

    window.fakeMovies[window.fakeMovies.length] = movie_name;

    if(document.all && window.fakeMovies) {
        for(i=0;i<window.fakeMovies.length;i++) {
            window[window.fakeMovies[i]] = new Object();
            var movie_name = window.fakeMovies[i];
            var fake_movie = window[movie_name];
            var real_movie = document.getElementById(movie_name);
            for(var method in fake_movie) {
                real_movie[method] = function() {
                    flash_function = "<invoke name=\"" + method.toString() + "\" returntype=\"javascript\">" + __flash__argumentsToXML(arguments, 0) + "</invoke>"; 
                    this.CallFunction(flash_function);
                }
            }
            window[movie_name] = real_movie;
        }
    }
}

this.get_size_type = function(size) {
    var m = 1048576;
    var k = 1024;
    if (size>m) {
        size = Math.floor((parseInt(size)/m)*10)/10 + 'M';
    } else if (size>k) {
        size = Math.floor((parseInt(size)/k)*10)/10 + 'K';
    } else {
        size = size + 'byte';
    }
    return size;
}

this.file_delete = function() {
    eval(gup_delete_file+"()");
}

this.delete_file = function() {
    var fl = document.getElementById(gup_name+"_files_list");
    var url = gup_path + "/guploader_delete.php";
    for (var i=0; i<fl.options.length; i++) {
        if (fl.options[i].selected==true) {
            file = this.get_file_info(fl.options[i].value);
            param = gup_after_upload_val + "&bf_no=" + file.file_num;
            op = fl.options[i];
            //var myAjax = new Ajax.Request(
            $.ajax ({
                type: 'post',
                url: url, 
                data: param,
                success : eval(gup_name + ".delete_file_complete")
            });
        }
    }
}

this.delete_file_complete = function() {
    var fl = document.getElementById(gup_name+"_files_list");
    if (!fl_width)
        fl_width = $("#"+gup_name+"_files_list").width();
    for (var i=0; i<fl.options.length; i++) {
        if (fl.options[i].selected==true) {
            op = fl.options[i];
            fl.removeChild(op);
        }
    }
    if (fl.options.length) 
        fl.options[fl.options.length-1].selected = true;

    $("#"+gup_name+"_files_list").css('width',fl_width+'px');
    eval(gup_name + ".preview()");
}

this.get_ext = function(file) {
    var ext = '';
    ext = file.split(".");
    ext = ext[ext.length-1].toLowerCase();
    return ext;
}

this.get_file_info = function(val) {
    var arr = val.split('|');
    var ret = {"id":arr[0], "file_num":arr[1], "save_name":arr[2], "real_name":arr[3], "size":arr[4]};
    return ret;
}

this.get_image_size = function(path) {
    var size    = new Array();
    var image   = new Image();
    image.src   = path;
    size[0]     = image.width;
    size[1]     = image.height;
    return size;
}

}// end of guploader
