function createXMLHttpRequest() {
    var xmlHttp = false;
    try {
        xmlHttp = new XMLHttpRequest();
    } catch (trymicrosoft) {
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (othermicrosoft) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (failed) {
            }
        }
    }
    return xmlHttp;
}

function ShowTr(rowindex, hid, t) {
    var row = $("hid-" + rowindex + "-" + hid);
    if (row == null) return;
    if (row.className.indexOf("hidden") == -1) {
        row.className = "urllist hidden";
        t.src = "http://www.9skb.com/templates/Default/style/images/plus.gif"
    } else {
        row.className = "urllist";
        t.src = "http://www.9skb.com/templates/Default/style/images/minus.gif";
        FillTrData(rowindex, hid);
    }
}

function FillTrData(rowindex, hid) {
    var url = "index.php?action=more&movie=" + hid;
    var http = createXMLHttpRequest();
    http.open("get", url, false);
    http.onreadystatechange = function (){
        if (http.readyState == 4 && http.status == 200) {
            $("hid-" + rowindex + "-" + hid + "-td").innerHTML = http.responseText;
        }
    };
    http.send(null);
    $("hid-" + rowindex + "-" + hid + "-td").innerHTML = http.responseText;
}

function $(obj) {
    return document.getElementById(obj);
}

function list_add(str) {
    var c = getCookie('mylist');
    if (c.indexOf(str) == -1) {
        setCookie('mylist', c + "|||" + str, 365);
    }
    list_show();
}

function list_del(str) {
    if (str == '*') {
        setCookie('mylist', '', 365);
    }
    else {
        var c = getCookie('mylist');
        c = c.replace('|||' + str, '');
        setCookie('mylist', c, 365);
    }
    list_show();
}

function list_show() {
    var c = getCookie('mylist');
    var l = c.split("|||");
    var h = '';
    for (var i = 0; i <= l.length; i++) {
        if (l[i] != undefined && l[i] != '') {
            h += l[i] + '<a href=javascript:list_del(\'' + l[i] + '\');>[X]</a> &nbsp;';
        }
    }
    if( h=='') h ='<font color=gray>列表为空</font>';
    $('mylist').innerHTML = h;
}

function list_play() {
    var c = getCookie('mylist');
    var l = c.split("|||");
    var h = '';
    for (var i = 0; i <= l.length; i++) {
        if (l[i] != undefined && l[i] != '') {
            h += '<a href="index.php?action=list&if=1&movie=' + l[i] + '" target=p>' + l[i] + '</a><BR/>';
        }
    }
    document.write(h);
}

function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}
function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
    return '';
}

window.onload = function(){
    try
    {
        list_show();
    }
    catch(e){}
};