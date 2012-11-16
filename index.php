<?php
require 'conf.php';
require './Lib/core.php';
require './Lib/templete.php';

$action = strtolower($_GET['action']);
$source = strtolower($_GET['source']);
$movie  = $_GET['movie'];

//兼容上一版本
if( isset($_GET['m']) && isset($_GET['s']) )
{
    header( "Location: /?action=".strtolower($_GET['m'])."&movie=".strtolower($_GET['s']) );
    exit;
}

//获取更多列表
if( $action === 'more' )
{
    $about_url = 'http://www.9skb.com/SResultItem/';
    $AJAX = SAE_GET($about_url.$s.'.html');
    $AJAX = iconv( 'GB2312' , 'UTF-8', $AJAX);
    echo $AJAX;
    die(0);
}
//搜索结果
else if ( $action==='list' )
{
    html_header($movie);
    if( $source=='flash')
    {
        html_list( $movie, flash_search($movie), $source );
    }
    elseif ( $source=='gvod' )
    {
        html_list( $movie, gvod_search($movie), $source );
    }
    else
    {
        html_list( $movie, qvod_search($movie), $source );
    }
    html_footer(0);
}
else
{
    html_header();
    html_form();
    html_footer(1);
}