<?php
header("Content-Type: text/plain");

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

//IMDB-TOP250-CN
function imdb()
{
    $d    = 'list';
    $f    = 'imdb_top250';
    $s    = new SaeStorage();
    $lastupdate = $s->getAttr( $d, $f);

    $now    = new DateTime('now');
    $ftime  = new DateTime( date('Y-m-d H:i:s', $lastupdate['datetime']) );
	$days   = $now->diff($ftime);

    if( $days->format('%d')>30 || !$s->fileExists($d,$f) )
    {
        $url  = 'http://www.imdb.cn/IMDB250TXT';
        $html = SAE_GET( $url );
        preg_match_all( '/<a href="([^"]+)" target="_blank">&nbsp;&nbsp;([^<\/]+)/i', $html, $imdb);
		$html = '';
		for( $i=0; $i<count( $imdb[2] ); $i++ )
        {
            if( $imdb[2][$i]!='' && $imdb[1][$i]!='' )
            	$html .= $imdb[2][$i].'|||'.$imdb[1][$i]."\n";
        }
        if( strlen($html)>50 )
        {
    	   	$s->delete( $d, $f );
	        $html = iconv('GB2312','UTF-8',$html);
	        $s->write( $d, $f, strtoupper($f)."\n".$html, -1, array(), false );
	        echo "imdb: 1, OK\n";
        }
        else
        {
	        echo "imdb: 1, FAILD\n";
        }
    }
    else
    {
        echo 'imdb: 0, '.$days->format('%d')."-days \n";
    }
}

//DOUBAN-TOP250-CN
function douban()
{
    $d    = 'list';
    $f    = 'douban_top250';    
    $s    = new SaeStorage();
    $lastupdate = $s->getAttr( $d, $f);

    $now    = new DateTime('now');
    $ftime  = new DateTime( date('Y-m-d H:i:s', $lastupdate['datetime']) );
	$days   = $now->diff($ftime);

    if( $days->format('%d')>30 || !$s->fileExists($d,$f)  )
    {
        $html = '';
        for( $i=1; $i<=25; $i++ )
            $html .= SAE_GET( 'http://kansha.baidu.com/collection/802?p='.$i );
        preg_match_all( '/<a href="([^"]+)" target="_blank">([^<]+)<\/a>/i', $html, $douban);
        $html = '';

		for( $i=0; $i<count( $douban[2] ); $i++ )
        {
            $douban[2][$i] = preg_replace( '/&nbsp;[^\(]+/i', '', $douban[2][$i] );
            if( $douban[2][$i]!='' && $douban[1][$i]!='' )
            	$html .= $douban[2][$i].'|||http://kansha.baidu.com/'.$douban[1][$i]."\n";
        }
        if( strlen($html)>50 )
        {
            $s->delete( $d, $f );
            $s->write( $d, $f, strtoupper($f)."\n".$html, -1, array(), false );
	        echo "douban: 1, OK\n";
        }
        else
        {
	        echo "douban: 1, FAILD\n";
        }
    }
    else
    {
        echo 'douban: 0, '.$days->format('%d')."-days \n";
    }
}

//MTIME-TOP100-CN
function mtime()
{
    $d    = 'list';
    $f    = 'mtime_top100';    
    $s    = new SaeStorage();
    $lastupdate = $s->getAttr( $d, $f);

    $now    = new DateTime('now');
    $ftime  = new DateTime( date('Y-m-d H:i:s', $lastupdate['datetime']) );
	$days   = $now->diff($ftime);

    if( $days->format('%d')>30 || !$s->fileExists($d,$f)  )
    {
	    $html = SAE_GET( 'http://www.mtime.com/top/movie/top100/' );
		for( $i=0; $i<10; $i++ )
        	$html .= SAE_GET( 'http://www.mtime.com/top/movie/top100/index-'.($i+1).'.html');        
        preg_match_all( '/<h2 class="[^"]+"><a class="[^"]+" href="([^"]+)" target="_blank">([^>]+)<\/a><\/h2>/i', $html, $mtime);
		$html = '';
		for( $i=0; $i<count( $mtime[2] ); $i++ )
        {
            $mtime[2][$i] = preg_replace( '/&nbsp;[^\(]+/i', '', $mtime[2][$i] );
            if( $mtime[2][$i]!='' && $mtime[1][$i]!='' )
            	$html .= $mtime[2][$i].'|||'.$mtime[1][$i]."\n";
        }
        if( strlen($html)>50 )
        {
    	   	$s->delete( $d, $f );
            $s->write( $d, $f, strtoupper($f)."\n".$html, -1, array(), false );
	        echo "mtime: 1, OK\n";
        }
        else
        {
	        echo "mtime: 1, FAILD\n";
        }
    }
    else
    {
        echo 'mtime: 0, '.$days->format('%d')."-days \n";
    }
}

//--最新上映--
function latest()
{
    $d    = 'list';
    $f    = '_newest';
    $s    = new SaeStorage();
    $lastupdate = $s->getAttr( $d, $f);

    $now    = new DateTime('now');
    $ftime  = new DateTime( date('Y-m-d H:i:s', $lastupdate['datetime']) );
	$days   = $now->diff($ftime);

    if( $days->format('%d')>0 || !$s->fileExists($d,$f)  )
    {
	    $html = SAE_GET( 'http://theater.mtime.com/China_Beijing/' );
		for( $i=1; $i<5; $i++ )
        	$html .= SAE_GET( 'http://movie.mtime.com/new/release/index-'.($i+1).'.html');        
        preg_match_all( '/,"Url":"([^"]+)","Title":"([^"]+)"}/i', $html, $mtime);
		$html = '';
		for( $i=0; $i<count( $mtime[2] ); $i++ )
        {
            $mtime[2][$i] = preg_replace( '/&nbsp;.*/i', '', $mtime[2][$i] );
            if( $mtime[2][$i]!='' && $mtime[1][$i]!='' )
            	$html .= $mtime[2][$i].'|||'.$mtime[1][$i]."\n";
        }
        if( strlen($html)>50 )
        {
    	   	$s->delete( $d, $f );
            $s->write( $d, $f, "最新上映(".date('Ymd').")\n".$html, -1, array(), false );
	        echo "mtime: 1, OK\n";
        }
        else
        {
	        echo "mtime: 1, FAILD\n";
        }
    }
    else
    {
        echo 'mtime: 0, '.$days->format('%d')."-days \n";
    }   
}

//--core---
latest();
imdb();
douban();
mtime();
