<html>
<head>
<title>YAOP</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="http://lib.sinaapp.com/js/bootstrap/3.0.0/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="index.css" />
</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/" >Yet Another Online Player</a>
        </div>
        
        <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
            <ul class="nav navbar-nav">
                <li><a href="javascript:void(0);" id="qvod">QVOD</a></li>
                <li><a href="javascript:void(0);" id="xigua">XIGUA</a></li>
                <li><a href="javascript:void(0);" id="gvod">GVOD</a></li>
                <li><a href="javascript:void(0);" id="list">TOP</a></li>
                <li><a href="javascript:void(0);" id="custom">最新/专辑</a></li>
                <li><a href="javascript:void(0);" id="itv">直播</a></li>
                <li style="color:#666;" title="version: 20140701" id="sae">SAE INSIDE</li>                
            </ul>
        </div>
    </div>
</div>

<div class="jumbotron">
        <form class="form-inline search" role="form">
            <div class="searchbox">
                <input type="text" class="form-control input-lg" id="movie" name="movie" value="冰与火之歌 第四季">
                <button type="button" class="btn btn-lg btn-primary" id="movie_btn">搜索@QVOD</button>
            </div>
        </form>
</div>

<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.8/jquery.min.js"></script>
<script type="text/javascript" src="http://lib.sinaapp.com/js/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="index.js?ver=<?php echo date('YmdHis');?>"></script>
</body>
</html>