<?php

$readme = '[1]. 搜索 -> 播放<BR>';
$readme .= '[2]. 排行榜: <a href=list.php>IMDB</a> / <a href=list.php>豆瓣</a> / <a href=list.php>时光网</a> / <a href="my.php?list=new">最新上映</a> / <a href="my.php?list=sp">专题</a><BR>';
$readme .= '[3]. 拾取列表: '.html_playlist().'<BR/>';
$readme .= '[4]. 自定义: <a href=my.php>临时列表</a> / <a href="my.php?list=create">创建我的列表</a> <BR>';

$original = '<BR>';
$original .= '数据来源: http://9skb.com/ | http://2tu.cc/  (带广告)<BR/>';
$original .= '在线播放: <a href=http://www.kuaibo.com/ targte=_blank>快播</a> | <a href=http://www.kankan.com/ targte=_blank>迅雷看看</a>';

$copyright = '<BR/>Updated ' . $version . ' <a href=my.php?list=admin><font color=white>列表管理</font></a>';

//HTML头部
function html_header( $title = null )
{
    $n = "\n";
    echo '<html>' . $n;
    echo '<head>' . $n;
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . $n;
    echo '<script language="javascript" type="text/javascript" src="/style/core.js"></script>' . $n;
    echo '<link rel="stylesheet" type="text/css" href="/style/core.css" />' . $n;
    if ( isset( $title ) ) {
        echo '<title>' . $title . '_QVOD_GVOD_电影搜索_在线播放</title>' . $n;
    } else {
        echo '<title>QVOD_GVOD_电影搜索_在线播放</title>' . $n;
    }
    if ( $_GET['if'] == '1' )
    {
        echo '<style type="text/css">';
        echo 'body {margin:0px;padding-left:5px;}';
        echo '#main,.containerborder  {width:100% !important; margin:0px !important;}';
        echo '</style>';
    }
    echo '</head>' . $n;
    echo '<body>' . $n. $n;
}


//HTML js播放列表
function html_playlist()
{
    $n = "\n";
    $ret ='<span id=mylist></span>' . $n;
    $ret .='<input type=button onclick="location.href=\'playlist.php\';" value="播放" />' . $n;
    $ret .='<input type=button onclick="list_del(\'*\');" value="清空" />' . $n;
    $ret .='<input type=button onclick="alert(\'点击 + 加入列表\n点击 x 从列表中删除\n点击清空按钮删除列表内所有已拾取电影\')" value="帮助" />' . $n;
    $ret .= $n;
    return $ret;
}


//HTML搜索框
function html_form( $search = null )
{
    $n = "\n";
    echo '<div id="main" style="width:75%;margin:100px 0 0 200px;line-height:32px;min-height:60px;">' . $n;
    echo '<form action="/" method="get" style="margin:0px;padding:0px;">' . $n;
    echo '<input type=text name=movie id=movie value="" style="width:500px;height:36px;font-size:24px;">' . $n;
    echo '<select name=source id=source style="width:100px;height:36px;text-align:center;font-size:24px;"><option selected value="qvod">QVOD</option><option value="gvod">GVOD</option></select>' . $n;
    echo '<input type=hidden name=action value=list>';
    echo '<input type=submit value=" 搜索 " style="width:100px;height:40px;font-size:20px;" />' . $n;
    echo '</form>' . $n;
    echo '</div>' . $n . $n;
}

//HTML搜索结果
function html_list( $s = null, $list = null, $f = null )
{
    $n = "\n";
    echo '<div id="main" style="width:75%;margin:20px auto;line-height:32px;">' . $n;
    echo '<form action="/" method="get" style="margin:0px;padding:0px;">' . $n;
    echo '<input type=text name=movie id=movie value="' . $s . '" style="width:500px;height:36px;font-size:24px;">' . $n;
    echo '<select name=source style="width:100px;height:36px;font-size:24px;">';
    echo '<option value="qvod" ' . ( $f == 'qvod' ? 'selected' : '' ) . '>QVOD</option>';
    echo '<option value="gvod" ' . ( $f == 'gvod' ? 'selected' : '' ) . '>GVOD</option>';
    echo '</select>' . $n;
    echo '<input type=hidden name=action value=list>';
    if ( $_GET['if'] == '1' )
    {
        echo '<input type=hidden name=if value=1>';
    }
    echo '<input type=submit value=" 搜索 " style="width:100px;height:40px;font-size:20px;">' . $n;
    echo '<input type=button value=" 返回 " style="width:100px;height:40px;font-size:20px;" onclick="top.location.href=\'/\';">' . $n;
    echo '</form>' . $n . $n;
    echo $list . $n . $n;
    echo '</div>' . $n;
}

//HTML尾部
function html_footer( $hasinfo = null )
{
    if ( $hasinfo ) {
        global $original, $readme, $copyright;
        echo "<div id='readme' style='clear:both;width:75%;margin:50px 0 0 200px;line-height:32px;color:#666;'><u>使用说明:</u><BR>$readme $original $copyright</div>\n\n";
    }
    echo '<div id=la><script language="javascript" type="text/javascript" src="http://js.users.51.la/3671790.js"></script></div>';
    echo '</body>';
    echo '</html>';
}
