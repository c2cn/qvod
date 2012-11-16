<?php
require 'conf.php';
require './Lib/core.php';
require './Lib/templete.php';

html_header("排行榜");
html_playlist();
$s_domain   = 'list';
$s          = new SaeStorage();
$mmc        = memcache_init();
$files      = $s->getList( $s_domain, '', 20 );
if ( count( $files ) < 1 )
{
    /*
     * 更新列表
     * 1) imdb
     * 2) 豆瓣
     * 3) mtime
    */
    echo "<div style='width:75%;margin:20px auto;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 排行榜</p>";
    imdb_list();
    douban_list();
    mtime_list();
    echo "正在跳转...";
    echo "</div>";
    echo "<meta http-equiv=refresh content=2,list.php>";
}
else
{
    echo "<div style='width:75%;margin:20px auto;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 排行榜</p>";
    echo "<p>拾取列表: ". html_playlist() ."</p>";
    foreach ( $files as $file )
    {
        echo "<div style='float:left;width:30%;margin-right:15px;height:500px; border:#333 3px solid;'>\n";
        if($mmc)
        {
            $key = str_replace('\\','',$file);
            $text = memcache_get($mmc,$key);
            if( !$text ) $text   = $s->read( $s_domain, $file );
            $text = '<span title="Cached" style="color:#222;" >[C]</span> '.$text;
        }
        $lastupdate = $s->getAttr($s_domain,$file);
        $movies = explode("\n",$text);
        array_pop($movies);
        echo "<div style='clear:both;width:auto;padding:5px;background-color:#000;color:#FFF;' title='[".date('Y-m-d',$lastupdate['datetime'])."]'>".array_shift($movies)."<span style='float:right;'><a href='playlist.php?d=".$s_domain."&f=".$file."' style='color:red;'>播放本列表</a></span></div>";
        echo "<div style='clear:both;width:auto;padding:5px;height:450px; overflow:scroll;'>";
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

        //每15天更新一次
        $now    = new DateTime('now');
        $ftime  = new DateTime( date('Y-m-d H:i:s', $lastupdate['datetime']) );
        $days   = $now->diff($ftime);
        if( $days->format('%d')>15 )
        {
            $s->delete($s_domain,$file);
        }
    }
    echo "</div>";
}
html_footer(0);

function imdb_list()
{
    global $s_domain,$s,$mmc;
    //IMDB
    $imdb_url   = 'http://www.imdb.cn/IMDB250TXT';
    $imdb_txt   = SAE_GET($imdb_url);
    $imdb_file  = "imdb_top_250";
    preg_match_all( '/<a href="(http:\/\/www\.imdb\.cn\/title\/tt\d+)" target="_blank">(.*)<\/a>/i', $imdb_txt, $imdb);
    $imdb_txt   = '';
    for($i = 0; $i<count($imdb[2]);$i++)
    {
        $u = $imdb[1][$i];
        $d = $imdb[2][$i];
        $d = str_ireplace( '&nbsp;', '', $d );
        if ( strpos( $d, "/" ) ) {
            $d = substr( $d, 0, strpos( $d, "/" ) );
        }
        $imdb_txt .= "$d|||$u\n";
    }
    $imdb_txt = iconv('GB2312','UTF-8',$imdb_txt);
    if( strlen($imdb_txt)<10 )
    {
        echo "IMDB_TOP250 列表更新失败!";
        die();
    }
    $imdb_txt = "IMDB_TOP250\n".$imdb_txt;
    if( $mmc )
    {
        memcache_set($mmc ,$imdb_file,$imdb_txt );
    }
    else
    {
        echo "memcache IMDB_TOP250 列表 失败!<br>";
    }
    $imdb_file = $s->write($s_domain,$imdb_file,$imdb_txt,-1,array(), false);
    if( !$imdb_file ) echo "IMDB_TOP250 列表保存失败!";
    echo "IMDB_TOP250 列表更新成功!<BR>";
}

function douban_list()
{
    global $s_domain,$s,$mmc;
    //豆瓣
    $douban_url = 'http://movie.douban.com/top250?format=text';
    $douban_txt = SAE_GET($douban_url);
    $douban_file = 'douban_top250';
    preg_match_all( '/<a href="(http:\/\/movie\.douban\.com\/subject\/\d+\/)">(.*)<\/a>/i', $douban_txt, $douban );
    $douban_txt = '';
    for ( $i = 0; $i < count( $douban[2] ); $i++ ) {
        $u = $douban[1][$i];
        $d = $douban[2][$i];
        $d = str_ireplace( '&nbsp;', '', $d );
        if ( strpos( $d, "/" ) ) {
            $d = substr( $d, 0, strpos( $d, "/" ) );
        }
        $douban_txt .= "$d|||$u\n";
    }
    if( strlen($douban_txt)<10 )
    {
        echo "豆瓣电影_TOP250 列表更新失败!";
        die();
    }
    $douban_txt = "豆瓣电影_TOP250\n".$douban_txt;
    if( $mmc )
    {
        memcache_set($mmc ,$douban_file,$douban_txt );
    }
    else
    {
        echo "memcache 豆瓣_TOP250 列表 失败!<br>";
    }
    $douban_file = $s->write($s_domain,$douban_file,$douban_txt,-1,array(), false);
    if( !$douban_file ) echo "豆瓣_TOP250 列表保存失败!";
    echo "豆瓣_TOP250 列表更新成功!<BR>";

}

function mtime_list()
{
    global $s_domain,$s,$mmc;
    //时光网
    $mtime_txt='';
    for($i=0;$i<10;$i++)
    {
        if($i==0)
        {
            $mtime_url = 'http://www.mtime.com/top/movie/top100/';
        }
        else
        {
            $mtime_url = 'http://www.mtime.com/top/movie/top100/index-'.($i+1).".html";
        }
        $mtime_tmp = SAE_GET($mtime_url);
        preg_match_all( '/<a class="c_blue" href="(http:\/\/movie\.mtime\.com\/\d+\/)" target="_blank">([^<]+)<\/a>/i', $mtime_tmp, $mtime );
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
    $mtime_file = 'mtime_top100';
    if( strlen($mtime_txt)<10 )
    {
        echo "时光网_TOP100 列表更新失败!";
        die();
    }
    $mtime_txt = "时光网_TOP100\n".$mtime_txt;
    if( $mmc )
    {
        memcache_set($mmc ,$mtime_file,$mtime_txt );
    }
    else
    {
        echo "memcache 时光网_TOP100 列表 失败!<br>";
    }
    $mtime_file = $s->write($s_domain,$mtime_file,$mtime_txt,-1,array(), false);
    if( !$mtime_file ) echo "时光网_TOP100 列表保存失败!";
    echo "时光网_TOP100 列表更新成功!<BR>";
}