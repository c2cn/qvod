<?php
require 'conf.php';
require './Lib/core.php';
require './Lib/templete.php';

html_header( "列表播放" );
//按照列表播放
$s        = new SaeStorage();
$s_domain = strtolower( $_GET['d'] );
$s_file   = strtolower( $_GET['f'] );
if ( $s->fileExists( $s_domain, $s_file ) ) {
    echo "<div style='width:100%;margin:0;'>";
    echo "<p>当前位置: <a href='/'>首页</a> &gt;&gt; 按列表播放</p>";
    echo "</div>";

    $text   = $s->read( $s_domain, $s_file );
    $movies = explode( "\n", $text );
    if ( $s_domain = 'custom' && substr( $s_file, 0, 4 ) == 'tmp_' ) {
        $text = "<div style='clear:both;width:auto;padding:5px;background-color:#000;color:#FFF;overflow:hidden;'>临时列表</div>";
    } else {
        $text = "<div style='clear:both;width:auto;padding:5px;background-color:#000;color:#FFF;overflow:hidden;'>" . array_shift( $movies ) . "</div>";
    }
    for ( $i = 0; $i < count( $movies ); $i++ ) {
        if ( strpos( $movies[$i], '|||' ) ) {
            $m = explode( "|||", $movies[$i] );
            $text .= ( $i + 1 ) . ") <a href=/?action=list&if=1&movie=" . ANSI( trim( $m[0] ) ) . " target=p>" . $m[0] . "</a> \n";
            $text .= "(<a href=" . $m[1] . " target=_blank title='影评及简介' target=p>info</a>)<BR>";
        } else if ( strlen( $movies[$i] ) > 0 ) {
            $text .= ( $i + 1 ) . ") <a href=/?action=list&if=1&movie=" . ANSI( trim( $movies[$i] ) ) . " target=p>" . $movies[$i] . "</a><BR>\n";
        }
    }
    echo <<<PLAY
    <div style="width:100%;margin:1px auto;">
        <div style="float:left;width:20%;border-right:#333 3px solid;min-height:99%;">
        $text
        </div>
        <div style="float:left;width:75%;margin-left:5px;">
        <iframe name=p src="javascript:(function(){document.write('点击左侧列表开始播放');})();" style="margin:0px;padding:0px;border:0px;width:100%;height:96%;"></iframe>
        </div>
    </div>
PLAY;
} else {
    echo <<<PLAY
            <div style='width:100%;margin:0;'>
            <p>当前位置: <a href='/'>首页</a> &gt;&gt; 拾取列表</p>
            </div>
    <div style="width:100%;margin:1px auto;">
        <div style="float:left;width:20%;border-right:#333 3px solid;min-height:99%;">
            <div style='clear:both;width:auto;padding:5px;background-color:#000;color:#FFF;overflow:hidden;'>我的拾取列表</div>
            <script>list_play();</script>
        </div>
        <div style="float:left;width:75%;">
        <iframe name=p src="javascript:(function(){document.write('点击左侧列表开始播放');})();" style="margin:0px;padding:0px;border:0px;width:100%;height:96%;"></iframe>
        </div>
    </div>
PLAY;
}
html_footer( 0 );