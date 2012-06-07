<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
	<link href="http://www.9skb.com/templates/Default/style/css/kbweb20120412.css" rel="stylesheet" type="text/css" />
	<?php
    $version = '2012.06.07';
	$action = $_GET['m'];
	$s      = $_GET['s'];

    //all list resource
	$list_url = 'http://www.9skb.com/?k=';

    //ajax list resouce
	$about_url = 'http://www.9skb.com/SResultItem/';

    //copyright
    $copyright = '<BR/><BR/><center style="font-size:15px;font-family:verdana;"><p>By <a href="http://www.6zou.net/" target="_blank">AJ</a>, ver  '.$version.'</p></center>';

    //---url fetch---
	function SAE_GET($url){
		$sae = new SaeFetchurl();
		$result = $sae->fetch($url);
		return $result;
	}

    //---chinese to ansi code---
	function ANSI($str){
		$strlength = strlen($str);
		$cstr = '';
		for($i = 0; $i < $strlength; $i++){
			$cstr .= "%".strtoupper(base_convert(ord($str{$i}), 10, 16));
		}
		return $cstr;
	}

    //----module & view---
	if($action=='ajax'){
        $AJAX = SAE_GET($about_url.$s.'.html');
        echo $AJAX;
	}elseif($action=='list'){
		?>
        <title>QVOD_<?php echo $s;?></title>
		</head>
		<body>
		<div id="main" style="width:888px;margin:10px auto;">
			<div style="text-align:center;margin-bottom:20px;">
				<span style="font-size:20px;color:red;font-weight:bold;"><?php echo $s;?></span>
				<span style="color:gray;"><a href='javascript:history.go(-1)'>BACK</a></span>
			</div>
            <?php
            $s = ANSI($s);
            $LIST = SAE_GET($list_url.$s);
            $sflag = strpos($LIST,'<div class="containerborder">');
            $eflag = strpos($LIST,'<div class="ResultPage">');
            if($sflag>0 && $eflag>0) {
                $LIST = substr($LIST,$sflag,$eflag-$sflag+strlen($eflag));
                $LIST = str_ireplace('/templates/','http://www.9skb.com/templates/',$LIST);
                $LIST = str_ireplace('<img onclick=','<img style="cursor:hand;" onclick=',$LIST);
                $LIST = preg_replace('/\/movie\/(\d+)\.html[^"]+/i','/goplay/$1.html',$LIST);
                $LIST = preg_replace('/\/goplay\/(\d+).html/i','http://www.9skb.com/goplay/$1.html',$LIST);
                echo $LIST;
            } else {
                echo "Nothing Found! <a href='javascript:history.go(-1)'>back</a>";
            }
            ?>
            <?php echo $copyright;?>
        </div>
        <script language="javascript">
            function createXMLHttpRequest()
            {
                var xmlHttp = false;
                try {
                    xmlHttp = new XMLHttpRequest();
                }
                catch (trymicrosoft) {
                    try {
                        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                    }
                    catch (othermicrosoft) {
                        try {
                            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        catch (failed) { }
                    }
                }
                return xmlHttp;
            }

            function ShowTr(rowindex, hid, t)
            {
                var row = $("hid-" + rowindex + "-" + hid);
                if (row == null) return;

                if (row.className.indexOf("hidden") == -1)
                {
                    $("AHID-" + hid).innerText = $("AHID-" + hid).innerText.replace('loading......','');
                    row.className = "urllist hidden";
                    t.src = "http://www.9skb.com/templates/Default/style/images/plus.gif"
                }
                else
                {
                    $("AHID-" + hid).innerText = "loading......"+$("AHID-" + hid).innerText;
                    $("hid-" + rowindex + "-" + hid + "-td").innerHTML = "<font color=\"red\">loading...</font>";
                    row.className = "urllist";
                    t.src = "http://www.9skb.com/templates/Default/style/images/minus.gif";
                    FillTrData(rowindex, hid);
                }
            }

            function FillTrData(rowindex, hid)
            {
                var mvname = $("AHID-" + hid).innerHTML;
                var url = "index.php?m=ajax&s="+hid;
                var http = createXMLHttpRequest();
                http.open("get", url, false);
                http.onreadystatechange = function() {
                    if (http.readyState == 4 && http.status == 200) {
                        $("hid-" + rowindex + "-" + hid + "-td").innerHTML = http.responseText;
                    }
                }
                http.send(null);
                $("hid-" + rowindex + "-" + hid + "-td").innerHTML = http.responseText;
            }

            function $(obj) {
                return document.getElementById(obj);
            }
        </script>
        <?
	}else{
		?>
		<title>QVOD_Searcher</title>
		</head>
		<body>
		<div id="main" style="width:888px;margin:10px auto;">
			<form action="#" method="get">
				<div style="margin:50px auto;padding:50px;width:80%;border:#333 5px solid;font-size:24px;line-height:36px;">
					QVOD MOVIE: 
					<input type=text name=s id=s value="" style="height:50px;font-size:36px;font-family:verdana;">
					<input type=submit value=" GET " style="height:50px;font-size:36px;font-family:verdana;" onclick="if(s.value!=''){location.href='index.php?m=list&s='+s.value;return false;}else{s.focus();return false;}">
					<p style="text-align:right;color:gray;font-size:14px;margin-top:15px;">
						full version here: http://www.9skb.com/
					</p>
				</div>
			</form>
            <?php echo $copyright;?>
        </div>
        <?php
	}
	?>
</body>
</html>