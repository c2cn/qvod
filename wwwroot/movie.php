<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="js/main.js" language="javascript"></script>
<?php
$hid = $_REQUEST["id"];

$url = "http://www.i9skb.com/movie/".$hid.".html";

$result = CURL_GET($url);

$sflag = strpos($result,'<div id="ResultInfo">');

$eflag = strpos($result,'<div class="ContentSide">');

if($sflag>0 && $eflag>0)
{
	$title = substr($result,strpos($result,'<h1>')+4,strpos($result,'</h1>')-4-strpos($result,'<h1>'));

	$result = substr($result,$sflag,$eflag-$sflag+8);
	
	echo "<title>".$title."</title>\n</head>\n<body>\n";

	$result = str_ireplace('/posters/','http://img.gvod.net/',$result);

	$result = str_ireplace('/list/?actor=','" rel="',$result);

	echo $result;
} else {
	echo "未找到相关资源! <a href='javascript:history.go(-1)'>返回</a>";
}

function CURL_GET($u)
{
	$ch = curl_init($u);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_HEADER,false);
	$str=curl_exec($ch);
	curl_close($ch);
	return $str;
}
?>
</body>
</html>