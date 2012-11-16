<?php
require 'conf.php';
require './Lib/core.php';
require './Lib/templete.php';

html_header("自定义列表");
html_playlist();
$action     = strtolower( $_GET['list'] );
$s_domain   = 'custom';
$s          = new SaeStorage();
$mmc        = memcache_init();

if( $action=='new' )
{
    $newest = list_new();
    $movies = explode("\n",$newest);

    echo "<div style='width:75%;margin:20px auto;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 最新上映</p>";
    echo "<p>拾取列表: ". html_playlist() ."</p>";
    echo "</div>";

    echo "<div style='clear:both;width:75%;margin: 1px auto;padding:5px;background-color:#000;color:#FFF;'>".array_shift($movies)." <a href='playlist.php?d=".$s_domain."&f=_newest' style='color:red;'>播放本列表</a></div>";
    echo "<div style='clear:both;width:75%;margin: 1px auto;padding:5px;height:450px; overflow:scroll;'>";
    for($i=0;$i<count($movies);$i++)
    {
        if( strpos($movies[$i],'|||') )
        {
            $m = explode("|||", $movies[$i]);
            echo ($i+1).") <a href=/?action=list&movie=".ANSI( trim($m[0]) ).">".$m[0]."</a>";
            echo "(<a href=".$m[1]." target=_blank title='影评及简介'>info</a>)";
            echo " <a href=javascript:list_add('".trim($m[0])."')>+</a>";
            echo "<BR>";
        }
        else if( strlen($movies[$i])>0 )
        {
            echo ($i+1).") <a href=/?action=list&movie=".ANSI( trim($movies[$i]) ).">".$movies[$i]."</a>";
            echo " <a href=javascript:list_add('".trim($movies[$i])."')>+</a>";
            echo "<BR>";
        }
    }
    echo "</div>";
    echo "</div>\n";
}
else if($action=='create')
{
    echo "<div style='width:75%;margin:20px auto;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 创建临时列表</p>";
    echo "</div>";

    list_create();
}
else if($action=='admin')
{
    echo "<div style='width:75%;margin:20px auto;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 创建专题列表</p>";
    echo "</div>";

    list_create(1);
}
else if($action=='admin_now')
{
    list_create(-1);
}
else if($action=='sp')
{
    echo "<div style='width:75%;margin:20px auto;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 专题电影列表</p>";
    echo "<p>拾取列表: ". html_playlist() ."</p>";
    echo "</div>";

    list_custom('cus_');
}
else
{
    echo "<div style='width:75%;margin:20px auto;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 临时列表</p>";
    echo "</div>";

    list_custom('tmp_');
}
html_footer(0);

function list_new()
{
    global $s_domain,$s,$mmc;
    //时光网
    $mtime_txt='';
    $mtime_file = '_newest';

    //检查是否需要更新,每7天更新一次
    if( $s->fileExists($s_domain,$mtime_file) )
    {
        $lastupdate = $s->getAttr($s_domain,$mtime_file);
        $now    = new DateTime('now');
        $ftime  = new DateTime( date('Y-m-d H:i:s', $lastupdate['datetime']) );
        $days   = $now->diff($ftime);
        if( $days->format('%d')>7 )
        {
            $s->delete($s_domain,$mtime_file);
            echo "文件已删除<br>";
            if($mmc)
            {
                memcache_set($mmc,$mtime_file,'');
                echo "缓存已删除<br>";
            }
        }
    }
    else
    {
        if($mmc)
        {
            memcache_set($mmc,$mtime_file,'');
            echo "缓存已删除<br>";
        }
    }

    //如果缓存有则直接读缓存
    if($mmc)
    {
        $mtime_txt = memcache_get($mmc,$mtime_file);
        if( strlen($mtime_txt)>10 )
        {
            return "<font color=#333333 title=Cached>[C]</font>".$mtime_txt;
        }
    }
    //缓存没有则重新获取
    for($i=0;$i<5;$i++)
    {
        if($i==0)
        {
            $mtime_url = 'http://movie.mtime.com/new/release/';
        }
        else
        {
            $mtime_url = 'http://movie.mtime.com/new/release/index-'.($i+1).".html";
        }
        $mtime_tmp = SAE_GET($mtime_url);
        preg_match_all( '/<h3><a\s+href="(http:\/\/movie\.mtime\.com\/\d+\/)"\s+target="_blank"\s+>([^<]+)<\/a><\/h3>/i', $mtime_tmp, $mtime );
        $mtime_tmp='';
        for ( $j = 0; $j < count( $mtime[2] ); $j++ )
        {
            $u = $mtime[1][$j];
            $d = $mtime[2][$j];
            if ( strpos( $d, "&nbsp;" ) ) {
                $d = substr( $d, 0, strpos( $d, "&nbsp;" ) );
            }
            $mtime_tmp .= "$d|||$u\n";
        }
        $mtime_txt.=$mtime_tmp;
    }
    if( strlen($mtime_txt)<10 )
    {
        echo "获取最新上映列表失败!<br>";
        die();
    }
    $mtime_txt = "最新上映(来自时光网)[".date('Y-m-d')."]\n".$mtime_txt;
    if( $mmc )
    {
        memcache_set($mmc ,$mtime_file,$mtime_txt );
    }
    else
    {
        echo "memcache 最新上映列表 失败!<br>";
    }
    $mtime_file = $s->write($s_domain,$mtime_file,$mtime_txt,-1,array(), false);
    if( !$mtime_file ) echo "最新上映列表保存失败!<br>";
    echo "最新上映列表更新成功!<BR>";
    return $mtime_txt;
}

function list_custom( $prefix='' )
{
    global $s_domain,$s,$mmc;
    $files = $s->getList( $s_domain, $prefix, 20 );
    echo "<div style='width:75%;margin:1px auto;'>\n";
    foreach ( $files as $file )
    {
        $attr = array('datetime');
        echo "<div style='float:left;width:30%;margin:10px;border:#333 3px solid;'>\n";
        $lastupdate = $s->getAttr($s_domain,$file);
        $text = $s->read($s_domain,$file);
        $movies = explode("\n",$text);

        if( substr($file,0,4)=='tmp_' )
        {
            echo "<div style='clear:both;width:auto;padding:5px;background-color:gray;color:#FFF;overflow:hidden;' title='临时列表仅保存一天: ".$file."'>临时列表 <a href='playlist.php?d=".$s_domain."&f=".$file."' style='color:red;'>播放本列表</a></div>";
        }
        else
        {
            echo "<div style='clear:both;width:auto;padding:5px;background-color:#000;color:#FFF;overflow:hidden;' title='用户永久列表: ".$file."'>".array_shift($movies)." <a href='playlist.php?d=".$s_domain."&f=".$file."' style='color:red;'>播放本列表</a></div>";
        }
        echo "<div style='clear:both;width:auto;padding:5px;min-height:150px; height:450px; overflow:scroll;'>";
        for($i=0;$i<count($movies);$i++)
        {
            if( strpos($movies[$i],'|||') )
            {
                $m = explode("|||", $movies[$i]);
                echo ($i+1).") <a href=/?action=list&movie=".ANSI( trim($m[0]) ).">".$m[0]."</a>";
                echo "(<a href=".$m[1]." target=_blank title='影评及简介'>info</a>";
                echo " <a href=javascript:list_add('".trim($m[0])."')>+</a>";
                echo "<BR>";
            }
            else if( strlen($movies[$i])>0 )
            {
                echo ($i+1).") <a href=/?action=list&movie=".ANSI( trim($movies[$i]) ).">".$movies[$i]."</a>";
                echo " <a href=javascript:list_add('".trim($movies[$i])."')>+</a>";
                echo "<BR>";
            }
        }
        echo "</div>";
        echo "</div>\n";

        //临时列表只保留一天
        $now    = new DateTime('now');
        $ftime  = new DateTime( date('Y-m-d H:i:s', $lastupdate['datetime']) );
        $days   = $now->diff($ftime);
        if( $days->format('%d')>1 && substr($file,0,4)=='tmp_')
        {
            $s->delete($s_domain,$file);
        }
    }
    echo "</div>";
}

function list_create($admin=0)
{
    global $s_domain,$s,$mmc;
    if( $admin==1 || $admin==-1 )
    {
        if($admin<0)
        {
            $filename = 'cus_'.md5(date('Y-m-d-H-i-s'));
        }
        else
        {
            $filename = 'cus_'.md5(date('Y-m-d-H'));
        }
        $admin = '管理员密码: <input name=password type=text value=""><BR/>';
    }
    else
    {
        $filename = 'tmp_'.md5(session_id());
        $admin = '';
    }
    if( $_SERVER['REQUEST_METHOD']=='GET' )
    {
        if($s->fileExists($s_domain,$filename))
        {
            $content = $s->read($s_domain,$filename);
        }
        else
        {
            $content='';
        }
        echo <<<FORM
        <form method="post" action="" style="width:75%;margin:20px auto;">
        文件名: $filename <font color=red><b>注意: 电影名每行一个, 未登陆用户列表仅保存1天</b></font>
        <div style="float:left;width:50%;">
            <textarea name=m style="width:100%;height:300px;overflow:auto;">$content</textarea><BR/><BR/>
        </div>
        <div style="float:left;width:40%;margin-left:30px;padding:5px;" title="示例">
        普罗米修斯<BR>
        黑客帝国<BR>
        星河战队<BR>
        当幸福来敲门<BR>
        肖申克的救赎<BR>
        ...
        </div>
        <div style="width:100%;clear:both;">
            $admin
            <button accesskey='s'>保存并播放(<u>S</u>)</button>
            <input type=button onclick="location.href='/';" value="返回首页" />
        <div>
        </form>
FORM;
    }
    else if( $_SERVER['REQUEST_METHOD']=='POST' )
    {
        global $adminpassword;
        if( $adminpassword=== $_POST['password'] )
        {
            $s->write($s_domain,$filename,$_POST['m'],-1,array(),false);
            echo "<h3>正在跳转...</h3><meta http-equiv=refresh content=1,playlist.php?d=".$s_domain."&f=".$filename.">";
        }
        else
        {
            die('<h1>权限不足!</h1>');
        }
    }
}