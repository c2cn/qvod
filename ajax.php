<?php
header('Content-Type: application/json');

//--get vars--
$source = isset( $_GET['source'] ) ? $_GET['source'] : '';
$movie  = isset( $_GET['movie']  ) ? $_GET['movie']  : '';


//--return--structrue--
$ret = array(
    'type' => 'json',
    'data' => array()
);

if( !class_exists('SaeFetchurl') ) require( 'fakeSAE.php' );
//SAE -> FetchUrl -> GET
function SAE_GET( $url )
{
    $sae    = new SaeFetchurl();
    $sae->setHeader( 'User-Agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36' );
    $sae->setHeader( 'Referer', $url );
    $result = $sae->fetch( $url );
    return $result;
}

//SAE -> FetchUrl -> POST
function SAE_POST( $url, $data )
{
    $sae = new SaeFetchurl();
    $sae->setHeader( 'User-Agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36' );
    $sae->setHeader( 'Referer', $url );
    $sae->setMethod( 'POST' );
    $sae->setPostData( $data );
    $result = $sae->fetch( $url );
    return $result;
}

//SAE -> SaeStorage -> READ
function SAE_READ( $domain='list', $file )
{
    $s = new SaeStorage();
    $r = $s->read( $domain, $file );
    if( !$r )
        return 'ERROR';
    return $r;
}

//SAE -> SaeStorage -> WRITE
function SAE_WRITE( $domain='list', $file, $str )
{
    $s = new SaeStorage();
    $r = $s->write( $domain, $file, $str );
    if( !$r )
        return 'ERROR';
    return $r;
}


//--dispatch--
function qvod( $kw )
{
	$url  = 'http://www.9skb.com/?k='.ansi_encode( iconv('UTF-8','GB2312',$kw) );
    $html = SAE_GET($url);
    $s_flag = strpos($html,'<div class="containerborder">');
    $e_flag = strpos($html,'<div class="ResultPage">');
    $html   = substr($html, $s_flag, $e_flag-$s_flag-strlen($s_flag) );
    $html   = iconv( 'GB2312' , 'UTF-8', $html);
        
    $html = preg_replace( '/<div id="result/si', "\n".'<div id="result', $html );
    $html = preg_replace( '/<(img|a|\/a)[^>]*>/i', '', $html );    
    $m = preg_match_all( '/<div id="result(\d+)" class="clear">.*<h4>(.*)<\/h4>.*<div class="size wcol3">(.*)<\/div><div class="play wcol7">.*<\/div><\/div>/i', $html, $results);    
    if( $m )
    {
        $results[1] = array_slice( $results[1], 0, 10);
        $results[2] = array_slice( $results[2], 0, 10);
        $results[3] = array_slice( $results[3], 0, 10);

        $tmp = join('|||', $results[3]);
		$tmp = preg_replace('/<div[^>]+>/i', '', $tmp);
        $tmp = preg_replace('/(<\/div>)+/i', ', ', $tmp);
        $results[3] = explode('|||', $tmp);
        
        for( $i=0; $i<count( $results[1] ); $i++ )
        {
            $results[1][$i] = '<a href="javascript:qvod_play(\''.$results[1][$i].'\');" class="btn btn-primary btn-sm">播放源</a><span id='.$results[1][$i].'></span>';
        }
	    return array( 'title'=>$results[2], 'play'=>$results[1], 'info'=>$results[3] );
    }
    else
    {
        return array('ERROR');
    }
}

function gvod($kw)
{
	$url  = 'http://www.2tu.cc/search.asp';
    $kw   = ansi_encode( iconv('UTF-8','GB2312',$kw) );
    $html = SAE_POST($url,'searchword='.$kw);
    $s_flag = strpos($html,'<ul class="mlist">');
    $e_flag = strpos($html,'</ul><div id="pages">');
    $html   = substr($html, $s_flag, $e_flag-$s_flag-strlen($s_flag) );
    $html   = iconv( 'GB2312' , 'UTF-8', $html);

    $html  = preg_replace( '/<li/si', "\n".'<li', $html );
    $m = preg_match_all( '/<li><a href="([^"]+)" title="([^"]+)" target="_blank" class="p">.*<\/a><em>(.*)<span><a href=.*/i', $html, $results);    
	if( $m )
    {
        $results[1] = array_slice( $results[1], 0, 10);
        $results[2] = array_slice( $results[2], 0, 10);
        $results[3] = array_slice( $results[3], 0, 10);

        $tmp = join('|||', $results[3]);
        $tmp = preg_replace('/<p>[^<]+<\/p>/i', '', $tmp);
        $tmp = preg_replace('/<(p|\/p)>/i', '', $tmp);
        $tmp = preg_replace('/<\/i><i>/i', ', ', $tmp);
        $results[3] = explode('|||', $tmp);
        
        for( $i=0; $i<count( $results[1] ); $i++ )
        {
            $results[1][$i] = '<a href="http://www.2tu.cc/'.$results[1][$i].'" title="播放源：http://www.2tu.cc/'.$results[1][$i].'" class="btn btn-primary btn-sm" target=_blank>播放源</a>';
        }
	    return array( 'title'=>$results[2], 'play'=>$results[1], 'info'=>$results[3] );
    }
    else
    {
        return array('ERROR');
    }
}

function xigua($kw)
{
	$url  = 'http://www.imxigua.com/index.php?s=vod-search-wd-'.$kw.'-1.html';
    $html = SAE_GET($url);
    $s_flag = strpos($html,'<div class="ul">');
    $e_flag = strpos($html,'<p class="page">');
    $html   = substr($html, $s_flag, $e_flag-$s_flag-strlen($s_flag) );

    $html  = preg_replace( '/<\/(p|h5)>\s+<p>/si', '</$1><p>', $html );
    $m = preg_match_all( '/<h5><a href="([^"]+)" title="([^"]+)">.*<\/a><label>\d+<\/label><\/h5><p>(主演.*)<p>地区/i', $html, $results);
	if( $m )
    {
        $results[1] = array_slice( $results[1], 0, 10);
        $results[2] = array_slice( $results[2], 0, 10);
        $results[3] = array_slice( $results[3], 0, 10);

        $tmp = join('|||', $results[3]);
        $tmp = preg_replace('/<[^>]+>/i', ' ', $tmp);
        $tmp = preg_replace('/\s\s+/i', ', ', $tmp);
        $results[3] = explode('|||', $tmp);
        
        for( $i=0; $i<count( $results[1] ); $i++ )
        {
            $results[1][$i] = '<a href="http://www.imxigua.com/'.$results[1][$i].'" title="播放源：http://www.imxigua.com/'.$results[1][$i].'" class="btn btn-primary btn-sm" target=_blank>播放源</a>';
        }
	    return array( 'title'=>$results[2], 'play'=>$results[1], 'info'=>$results[3] );
    }
    else
    {
        return array('ERROR');
    }
}

//--all custom list--
function list_custom()
{
    $domain = 'custom';
	$s      = new SaeStorage();
    $files  = $s->getList( $domain, '', 10 );
    $list   = array();
    foreach ( $files as $file )
    {
        array_push( $list, $s->read($domain,$file) );
    }
    return $list;
}

//--ansi encode--
function ansi_encode( $str )
{
    $strlength = strlen( $str );
    $cstr      = '';
    for ( $i = 0; $i < $strlength; $i++ ) {
        $cstr .= "%" . strtoupper( base_convert( ord( $str{$i} ), 10, 16 ) );
    }
    return $cstr;
}

//--core--
if( $movie!='' )
{
    switch ($source)
	{
        case 'list':
	        $ret['type'] = 'json';
			$ret['data'] = array( 'title'=>$movie, 'list'=>array( SAE_READ('list',$movie) ) );
	        break;
        case 'custom':
	        $ret['type'] = 'json';
			$ret['data'] = list_custom();
	        break;
        case 'gvod':
        	$ret['type'] = 'json';
            $ret['data'] = gvod($movie);
        	break;
        case 'xigua':
        	$ret['type'] = 'json';
            $ret['data'] = xigua($movie);
        	break;
        default:
        	$ret['type'] = 'json';
            $ret['data'] = qvod($movie);
	}
    if( count($ret['data'])==1 )
        if( $ret['data'][0]=="ERROR" )
        {
            $ret['type']='html';
            $ret['data'] = array('未找到资源，切换引擎或关键词再试一次吧');
        }
}
else
{
    if( $source!='' )
	{
        $tmp = SAE_GET($source);
        preg_match_all( '/<a href="([^"]+)"/', $tmp, $results);
        $results[1] = array_slice( $results[1], 0, 10);

        $ret['type'] = 'json';
		$ret['data'] = $results[1];
	}
    else
    {
		$ret['type'] = 'html';
        $ret['data'] = array('未找到资源，切换引擎或关键词再试一次吧');
    }
}    
die( json_encode($ret) );
