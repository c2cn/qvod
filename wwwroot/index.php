<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<title>QVOD 资源搜索</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="js/main.js" language="javascript"></script>
</head>
<body>
<?php
$version = "2011.12.31";

function CURL_GET($u){
	$ch = curl_init($u);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_HEADER,false);
	$str=curl_exec($ch);
	curl_close($ch);
	return $str;
}

function convertStr($str) {
	$strlength = strlen($str);
	$cstr = '';
	for($i = 0; $i < $strlength; $i++) {
		$cstr .= "%".strtoupper(base_convert(ord($str{$i}), 10, 16));
	}
	return $cstr;
	/*
	return rawurlencode(mb_convert_encoding($str, 'gb2312', 'utf-8'));
	*/
}

$title = isset($_REQUEST["s"])?$_REQUEST["s"]:"";
$title_cn = $title;
//$title_cn = mb_convert_encoding($title,'gb2312','utf-8');

if ($title==""){

	echo <<<END
	<form action="" method="get">
	<div style="margin:50px auto;padding:50px;width:600px;border:#333 10px solid;">
	QVOD资源搜索: 
	<input type=text name=s id=s value="">
	<input type=submit value=" 搜索 " onclick="if(s.value==''){alert('不能为空!');s.focus();return false;}else{location.href='index.php?s='+s.value;return false;}">
	</form>
	</div>
END;
} else {
	?>
	<script>document.title="<?php echo "QVOD搜索结果: ".$title_cn;?>";</script>
	<div style="text-align:center">
		<span style="font-size:18px;color:red;"><?php echo $title_cn;?></span> <span style="color:gray;">( 本站只提供片源索引, 观察影片需点击加号(+)选择片源观看。<a href='javascript:history.go(-1)'>返回</a> )</span>
		<BR/>
	</div>
	<hr>
	<?
	$title = convertStr($title);
	$source = "http://www.9skb.cc/?k=";
	$url = $source.$title;
	$result = CURL_GET($url);
	$sflag = strpos($result,'<table id="ResultTb"');
	$eflag = strpos($result,'</table>');
	if($sflag>0 && $eflag>0) {
		$result = substr($result,$sflag,$eflag-$sflag+8);
		$result = str_ireplace('/templates/Default/images/hot/','images/',$result);
		$result = str_ireplace('/templates/Default/images/','images/',$result);
		$result = preg_replace('/<a href="\/movie\/(\d+)\.html\?.*" id=/i',' &nbsp;<a href="movie.php?id=$1" id=',$result);
		$result = preg_replace('/<img src=.*\/>\s/i','',$result);
		echo $result;
	} else {
		echo "未找到相关资源! <a href='javascript:history.go(-1)'>返回</a>";
	}

}
?>
<center><p>powered by FB.team.AJ ver <?php echo $version;?></p></center>
</body>
</html>