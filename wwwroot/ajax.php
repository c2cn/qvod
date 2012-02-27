<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<?php
$hid = $_REQUEST["id"];

$url = "http://www.9skb.info/SResultItem/".$hid.".html";

$result = CURL_GET($url);

echo $result;

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