<?php
//SAE -> FetchUrl -> GET
function SAE_GET( $url )
{
    $sae    = new SaeFetchurl();
    $result = $sae->fetch( $url );
    return $result;
}

//SAE -> FetchUrl -> POST
function SAE_POST( $url, $data )
{
    $sae = new SaeFetchurl();
    $sae->setMethod( 'POST' );
    $sae->setPostData( $data );
    $result = $sae->fetch( $url );
    return $result;
}

//中文ANSI编码
function ANSI( $str )
{
    $strlength = strlen( $str );
    $cstr      = '';
    for ( $i = 0; $i < $strlength; $i++ ) {
        $cstr .= "%" . strtoupper( base_convert( ord( $str{$i} ), 10, 16 ) );
    }
    return $cstr;
}

//QVOD搜索
function qvod_search($s = null)
{
    //search url
    $list_url = 'http://www.9skb.com/?k=';
    $list_url = $list_url.ANSI( iconv('UTF-8','GB2312',$s) );
    $LIST = SAE_GET( $list_url );
    $LIST = preg_replace('/title="[^"]+"/i','',$LIST);
    $sflag = strpos($LIST,'<div class="containerborder">');
    $eflag = strpos($LIST,'<div class="ResultPage">');
    if($sflag>0 && $eflag>0)
    {
        $LIST = substr($LIST,$sflag,$eflag-$sflag-strlen($sflag));
        $LIST = iconv( 'GB2312' , 'UTF-8', $LIST);
        $LIST = str_ireplace('/templates/','http://www.9skb.com/templates/',$LIST);
        $LIST = str_ireplace('href="/goplay/','href="http://www.9skb.com/goplay/',$LIST);
        $LIST = str_ireplace('<img onclick=','<img style="cursor:hand;" onclick=',$LIST);
        $LIST = preg_replace('/\/movie\/(\d+)\.html/i','http://www.9skb.com/goplay/$1.html',$LIST);
        $LIST = preg_replace('/(<h4>|<\/h4>)/i','',$LIST);
    }
    else
    {
        $LIST = "<div style='margin:50px;font-weight:bold;color:red;font-size:15px;'>未找到[ ".$s." ]相关电影!</div>";
    }
    $original_url = "<p>原始数据(带广告)：<a href=$list_url target=_blank>$list_url</a></p>";
    $LIST = $original_url.$LIST."<hr>";
    return $LIST;
}

//GVOD搜索
function gvod_search($s)
{
    //search url
    $list_url = 'http://www.2tu.cc/search.asp';
    $s = ANSI( iconv('UTF-8','GB2312',$s) );
    $LIST = SAE_POST($list_url,'searchword='.$s);
    $s_flag = strpos($LIST,'<div class="rightsideBar fl">');
    $e_flag = strpos($LIST,'<div class="page cb tr">');
    if( $s_flag>0 && $e_flag>0 )
    {
        $LIST = substr($LIST,$s_flag,$e_flag-$s_flag);
        $LIST = iconv('GB2312','UTF-8',$LIST );
        preg_match_all('/<h3><a href=[^>]+>(.*)<\/a>/i', $LIST, $name);
        preg_match_all('/<p><a href="([^"]+)" class="btn-play"><\/a>/i', $LIST, $play);
        $LIST = '';
        for($i=0;$i<count($name[1]);$i++)
        {
            $LIST .= "<p>".($i+1).") <a href=http://www.2tu.cc".$play[1][$i]." target=_blank><font color=red>《". $name[1][$i]."》</font></a>, 播放地址: <a href=http://www.2tu.cc".$play[1][$i]." target=_blank>http://www.2tu.cc".$play[1][$i]."</a></p>";
        }
    }
    else
    {
        $LIST = "<div style='margin:50px;font-weight:bold;color:red;font-size:15px;'>未找到[ ".$s." ]相关电影!</div>";
    }
    return $LIST;
}