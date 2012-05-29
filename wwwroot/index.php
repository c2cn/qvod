<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<title>QVOD 资源搜索</title>
<link href="http://www.9skb.com/templates/Default/style/css/kbweb20120412.css" rel="stylesheet" type="text/css" />
<script src="ajax.js" language="javascript"></script>
</head>
<body>
<div id="main" style="width:888px;margin:10px auto;">
<?php
$version = "2012.05.29";

function CURL_GET($u)
{
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
}
$title = isset($_REQUEST["s"])?$_REQUEST["s"]:"";
$title_cn = $title;

if ($title=="")
{

	echo <<<END
	<form action="index.php" method="get">
	<div style="margin:50px auto;padding:50px;width:80%;border:#333 5px solid;font-size:24px;line-height:36px;">
	QVOD资源(数据来源: http://www.9skb.com/): <BR/><BR/>
	<input type=text name=s id=s value="" style="height:50px;font-size:36px;font-family:verdana;"> 
	<input type=submit value=" 搜索 " style="height:50px;font-size:36px;font-family:verdana;" onclick="if(s.value==''){alert('不能为空!');s.focus();return false;}else{location.href='index.php?s='+s.value;return false;}">
	</form>
	</div>
END;
} else {
	?>
	<script>document.title="<?php echo "QVOD搜索结果: ".$title_cn;?>";</script>
	<div style="text-align:center">
		<span style="font-size:18px;color:red;"><?php echo $title_cn;?></span>
		<span style="color:gray;">
			(
				本站数据全部来自: http://www.9skb.com/
				<a href='javascript:history.go(-1)'>返回</a> 
			)
			</span>
	</div>
	<BR/><BR/>
	<?php

	$title = convertStr($title);

	$source = "http://www.9skb.com/?k=";

	$url = $source.$title;

	$result = CURL_GET($url);
	$sflag = strpos($result,'<div class="containerborder">');
	$eflag = strpos($result,'<div class="ResultPage">');

	if($sflag>0 && $eflag>0) {
		$result = substr($result,$sflag,$eflag-$sflag+strlen($eflag));
		$result = str_ireplace('/templates/','http://www.9skb.com/templates/',$result);
		$result = str_ireplace('<img onclick=','<img style="cursor:hand;" onclick=',$result);
		$result = preg_replace('/\/movie\/(\d+)\.html[^"]+/i','/goplay/$1.html',$result);
		$result = preg_replace('/\/goplay\/(\d+).html/i','http://www.9skb.com/goplay/$1.html',$result);
		echo $result;
	} else {
		echo "未找到相关资源! <a href='javascript:history.go(-1)'>返回</a>";
	}

}
?>
	<BR/><BR/><center style="font-size:15px;font-family:verdana;"><p>Powered by <a href='http://www.6zou.net/'>XTEAM.AJ</a>, last update <?php echo $version;?></p></center>
</div>

</body>
</html>