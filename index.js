//qvod获取播放源
function qvod_play(id){
    if( typeof($)!='undefined' )
    {
        url = 'ajax.php?source='+ encodeURIComponent('http://www.9skb.com/SResultItem/'+id+'.html');
        $.getJSON(url,function( ret ){
	        for( i=0; i<ret.data.length; i++ )
            	$('#'+id).append( ' <a href="'+ ret.data[i] +'" title="资源地址：'+ ret.data[i] +'" class="btn btn-default btn-sm" target=_blank>'+(i+1)+'</a> ' );
        });
    }
    else
    {
        alert( 'jQuery not loaded!' );
    }
};

function resetbox()
{
    if( $('#restable').length>0 )
		$('#restable').remove();

	if( $('table[name="list"]').length>0 )
    	$('table[name="list"]').remove();

    $('.searchbox').css('display','').css('top','35%');
};

function hidebox()
{
    if( $('#restable').length>0 )
		$('#restable').remove();

	if( $('table[name="list"]').length>0 )
    	$('table[name="list"]').remove();

	$('.searchbox').css('display','none');
};

function loading(x)
{
	if(x)
        $('.searchbox').after('<div id=loading style="min-height:100%; height:100%; position:absolute; top:0; left:0; botttom:0; right:0; margin:auto; padding:200px; text-align:center; color:red;"><h1>loading...</h1></div>');
	else
        $('#loading').animate({'top':'0px'}, 'slow').remove();
}

function showlist( id )
{
    $('#'+id+' tr').each(function(i,e){
        if( i>10 )
        {
            if( $(this).css('display')=='none' )
                $(this).css('display','');
			else
              	$(this).css('display','none');
        }
	});    
    if( $('#'+id+' tr:last-child').css('display')=='none' )
        $( '#updown'+id.replace('custom_','') ).removeClass().addClass('glyphicon glyphicon-minus');
    else
        $( '#updown'+id.replace('custom_','') ).removeClass().addClass('glyphicon glyphicon-plus');
};

function add2list( movie )
{
    $('#movie').val( movie );
    $('#qvod').trigger('click');    
    $('#movie_btn').trigger('click');
};

$(function(){
    //默认源
	source = 'qvod';
    $('#movie').dblclick(function(){
        val = $('#movie').val();
        val = val.replace( /\(.*\)/g,'');
    	$('#movie').val( val );
    }).attr('title','双击可去掉括号内容');
    
    //切换源
    $('#qvod').click( function() { resetbox(); source='qvod';  $('#movie_btn').removeClass().addClass('btn btn-primary btn-lg').text('搜索@'+source.toUpperCase());} );
    $('#gvod').click( function() { resetbox(); source='gvod';  $('#movie_btn').removeClass().addClass('btn btn-success btn-lg').text('搜索@'+source.toUpperCase());} );
    $('#xigua').click(function() { resetbox(); source='xigua'; $('#movie_btn').removeClass().addClass('btn btn-warning btn-lg').text('搜索@'+source.toUpperCase());} );
    $('#list').click(function(){
		hidebox();
        $('.searchbox').animate({'top':'0px'}, function(){
            toplist = ['_newest', 'imdb_top250', 'douban_top250', 'mtime_top100'];
            if( $('table[name="list"]').length>0 )
                $('table[name="list"]').remove();
            for( i=0; i<toplist.length; i++){
                url = 'ajax.php?source=list&movie='+toplist[i];
                tbl = '<table name=list id='+ toplist[i] +' class="table table-bordered" style="float:left; width:auto; margin-top:20px; margin-right:5px; font-size:12px; "><tr><td style="background-color:#222;color:#FFF;" colspan=3>list: '+ toplist[i] +'</td></tr><tr><td>loading...</td></tr></table>';
                $('.searchbox').before( tbl );
                $.getJSON( url, function( ret ){
		            if( ret.type=='json' )
		            {
                        tblid  = ret.data.title;
                        tbllst = ret.data.list;
                        if( tbllst[0].length>10 ){
                            tbllst = tbllst[0].split('\n');
                            $('#'+tblid).children().remove();
                            $('#'+tblid).append( '<tr><td style="background-color:#222;color:#FFF;" colspan=3>'+ tbllst[0] +'</td></tr>' );
                            for( i=1; i<tbllst.length; i++ )
                            {
                                mname = tbllst[i].split('|||')[0];
                                murl  = tbllst[i].split('|||')[1];
                                if( mname!='' && murl!='' )
                                    $('#'+tblid).append( '<tr><td>'+ i +'</td><td>'+ mname.replace( /\((\d+)\)/, '<span style="position:absolute;margin-top:-10px;font-size:10px;">$1</span>' ) +'</td><td><a href='+murl+' target=_blank class="btn btn-default btn-xs">详情</a> <a href="javascript:add2list(\''+mname+'\')" target=_blank class="btn btn-primary btn-xs">播放</a></td></tr>' );
                            }
                        }
		            }
                });
            }
        });
    });
    
    $('#custom').click(function(){
		hidebox();
        $('.searchbox').animate({'top':'0px'}, function(){
            if( $('table[name="list"]').length>0 )
                $('table[name="list"]').remove();
			url = 'ajax.php?source=custom&movie=*';
			$('.searchbox').after( '<table name=list style="margin:100px;width:300px;"><tr><td class=well>读取列表中...</td></tr></table>' );
			$.getJSON( url, function( ret ){
				if( ret.type=='json' )
				{
                	tbllst = ret.data;
                    if(tbllst.length<1){
                        $('.searchbox').after( '<table name=list style="margin:100px;width:300px;"><tr><td class=well>暂无</td></tr></table>' );
                        return;
                    }
	    	        if( $('table[name="list"]').length>0 )
    	    	        $('table[name="list"]').remove();
                    for(i=tbllst.length-1; i>=0; i--)
                    {
                        tbl = '<table name=list id=custom_'+ i +' class="table table-bordered" style="float:left; width:auto; min-width:19%; margin-top:20px; margin-right:5px; font-size:12px; "></table>';
                        $('.searchbox').after( tbl );
                        
                        tmplst = tbllst[i].split('\n');
                        $('#custom_'+i).append( '<tr><td style="background-color:#222;color:#FFF;" colspan=3>'+ tmplst[0] +' -- 共 '+ (tmplst.length-2) +' 部 <div style="float:right;"><a href="javascript:showlist(\'custom_'+i+'\')"><span id=updown'+ i +' class="glyphicon glyphicon-plus" style="color:#FFF;font-weight:bold;font-size:18px;"></span></a></div></td></tr>' );
                        for( j=1; j<tmplst.length; j++)
                        {
                            if( tmplst[j] )
                            {
                                mname = tmplst[j].split('|||')[0];
                                murl  = tmplst[j].split('|||')[1];
								if( j<11 )
                                {
                                    if( murl && mname )
                                        $('#custom_'+i).append( '<tr><td>'+ j +'</td><td>'+ mname +'</td><td><a href='+murl+' target=_blank class="btn btn-default btn-xs">详情</a> <a href="javascript:add2list(\''+mname+'\')" target=_blank class="btn btn-primary btn-xs">播放</a></td></tr>' );
                                    else
                                        $('#custom_'+i).append( '<tr><td>'+ j +'</td><td>'+ mname +'</td><td><a href="http://movie.douban.com/subject_search?search_text='+mname+'" target=_blank class="btn btn-default btn-xs">详情</a> <a href="javascript:add2list(\''+mname+'\')" target=_blank class="btn btn-primary btn-xs">播放</a></td></tr>' );
                                }
                                else
                                {
                                    if( murl && mname )
                                        $('#custom_'+i).append( '<tr style="display:none;"><td>'+ j +'</td><td>'+ mname +'</td><td><a href='+murl+' target=_blank class="btn btn-default btn-xs">详情</a> <a href="javascript:add2list(\''+mname+'\')" target=_blank class="btn btn-primary btn-xs">播放</a></td></tr>' );
                                    else
                                        $('#custom_'+i).append( '<tr style="display:none;"><td>'+ j +'</td><td>'+ mname +'</td><td><a href="http://movie.douban.com/subject_search?search_text='+mname+'" target=_blank class="btn btn-default btn-xs">详情</a> <a href="javascript:add2list(\''+mname+'\')" target=_blank class="btn btn-primary btn-xs">播放</a></td></tr>' );
                                }
                            }
                        }
                        if( j>10 )
	                        $('#custom_'+i).append( '<tr><td colspan=3 align=right onclick="showlist(\'custom_'+i+'\')">...</td></tr>' );
                    }
				}
			});
        });
    });
    
    $('#itv').click(function(){
        hidebox();
        loading(1);
        resdiv = '<iframe id=restable style="height:100%;width:100%;margin:0;padding:0;border:0;margin-top:20px;" src="http://live.64ma.com/tv/live.html" frameborder="0" scrolling="no"></iframe>';
        $('.searchbox').after( resdiv );
        $('.searchbox').animate({'top':'0px'}, function(){
            $('#restable').css('height','567px');
			$('#restable').css('margin-top','10px').css('display','');
            loading(0);
        });
    });
    
    $('#sae').hover(function(){
        $('#restable').remove();
        resdiv  = '<div id=restable style="width:50%; position:absolute; top:25%; left:25%; botttom:0; right:0; padding:20px; border-radius:10px; background-color:#222; color:#FFF;">'
        resdiv += '自用系统，随时升级，如有意见，请自己保留。<br>';
        resdiv += 'QVOD来源：<a href="http://www.9skb.com/" target=_blank>http://www.9skb.com/</a><br>';
        resdiv += 'GVOD来源：<a href="http://www.2tu.cc/" target=_blank>http://www.2tu.cc/</a><br>';
        resdiv += 'XIGUA来源：<a href="http://www.imxigua.com/" target=_blank>http://www.imxigua.com/</a><br>';
        resdiv += 'TOP来源：<a href="http://www.imdb.cn/" target=_blank>imdb</a> <a href="http://www.douban.com/" target=_blank>豆瓣</a> <a href="http://www.mtime.com/" target=_blank>时光网</a><br>';
        resdiv += 'ITV来源：<a href="http://www.10086yes.com/" target=_blank>http://www.10086yes.com/</a><br>';
        resdiv += '</div>';
        $('.searchbox').after(resdiv);
        $('#restable').click(function(){ $('#restable').remove() }).delay(10000).fadeOut(function(){ $('#restable').remove() });
    });
    
    //搜索按钮
    $('#movie_btn').click(function(){
        movie = $('#movie').val();
        if( movie.length<1 )
        {
            $('#movie').focus();
            return false;
        }
        
        url = 'ajax.php?source='+source+'&movie='+ encodeURIComponent(movie);
        resdiv = '<table id=restable class="table table-bordered" style="display:none;"></table>';
        loading(1);
        $.getJSON(url,function( ret ){
            if( $('#restable').length>0 ) $('#restable').remove();
            $('.searchbox').after( resdiv );
            $('.searchbox').animate({'top':'60px'}, function(){
				$('.table').css('margin-top','80px').css('display','');
            });

            if( ret.type=='json' )
            {
                if( typeof( ret.data.title )!='undefined' ) 
	                for( i=0; i<ret.data.title.length; i++ )
			           	$('#restable').append( '<tr><td width="30%">'+ ret.data.title[i] +'</td><td>'+ ret.data.info[i] +'</td><td>'+ ret.data.play[i] +'</td></tr>' );
            }
            else
            {
                $('#restable').toggleClass('table-bordered').append( '<tr><td width="30%" style="border:0px;"></td><td style="border:0px;">'+ ret.data[0] +'</td><td width="30%" style="border:0px;"></td></tr>' );
            }
	        loading(0);
        });
    });
});
