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
	if (row == null)
		return;
	if (row.style.display != "none") {
		row.style.display = "none";
		t.src = "images/plus.gif"
	}
	else {
		row.style.display = "";
		$("hid-" + rowindex + "-" + hid + "-td").innerHTML = "其他来源：<font color=\"red\">loading...</font>";
		t.src = "images/minus.gif";
		FillTrData(rowindex, hid);
	}
}

function FillTrData(rowindex, hid)
{
	var mvname = $("AHID-" + hid).innerHTML;
	var url = "ajax.php?id="+hid;
	var http = createXMLHttpRequest();
	http.open("get", url, false);
	http.onreadystatechange = function() {
		if (http.readyState == 4 && http.status == 200) {
			$("hid-" + rowindex + "-" + hid + "-td").innerHTML = http.responseText;
		}
	}
	http.send(null);
	$("hid-" + rowindex + "-" + hid + "-td").innerHTML = "QVOD源: " + http.responseText;
}

function $(obj) {
	return document.getElementById(obj);
}