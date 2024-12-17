var set_life		= 1500;
var activity_life	= set_life;
function counter_life() {
	setInterval(function(){
		activity_life--;
		$('#counter').text(activity_life);
	},1000);
}
function lang(e) {
	return $('lang[key="' + e + '"]').attr('value');
}
function screen_fix() {
	var t = parseInt($('.navigation-bar').height()) + parseInt($('.panel-page').height()) + 3;
	$('.fixed-content').css({ 'top': t + 'px' });
	if($('.screen-fix table.dataTable .table-fixed').length == 0)
		$('.screen-fix table.dataTable').wrap('<div class="table-fixed"></div>');
	if($('.fg-header').length > 1) {
		var fgheader = 0;
		$('.fg-header').each(function(){
			var new_p = t + parseInt($(this).height()) + 9;
			if(new_p > fgheader) fgheader = new_p;
		});
		$('.fg-header').css({ 'top': (parseInt($('.panel-page').height()) + 3) + 'px' });
		$('.table-fixed').css({ 'top': fgheader + 'px' });
	} else {
		$('.fg-header').css({ 'top': (parseInt($('.panel-page').height()) + 3) + 'px' });
		$('.table-fixed').css({ 'top': (t + parseInt($('.fg-header').height()) + 7 ) + 'px' });
	}
}
function freeze_table() {
	if($.browser.mozilla || $.browser.webkit) {
		if($('table.table-freeze').length == 1) {
			$('.fixed-content').scrollTop(0);
			$('.freeze-table.header').remove();
			var class_table	= $('.table-freeze').attr('class');
			var rm_class	= class_table.replace('table-freeze','');
			var add_class	= rm_class.replace('table-download','');
			var konten		= '<table class="'+add_class+'">';
			konten += '<thead>';
			konten += $('.table-freeze thead').html();
			konten += '</thead>';
			konten += '</table>';
			$('.table-freeze').parent().prepend('<div class="freeze-table header">'+konten+'</div>');
			var arr	= [];
			$('.table-freeze thead th').each(function(i,j){
				arr[i]	= parseFloat($(this).width()) + 9;
				if($.browser.webkit) {
					arr[i] += 1;
				}
			});
			$('.freeze-table.header table thead th').each(function(i,j){
				$(this).css({'min-width':arr[i]});
			});
			if($('table.table-freeze').attr('col-freeze')) {
				$('.fixed-content').scrollLeft(0);
				$('.freeze-table.body').remove();
				$('.freeze-table.body-header').remove();
				var konten		= '<table class="'+add_class+'">';
				var col_freeze 	= $('table.table-freeze').attr('col-freeze');
				var e			= col_freeze.split(',');
				konten += '<thead></tr>';
				$.each(e,function(i,v){
					var body_tl	= $('table.table-freeze thead tr:nth-child(1) th:nth-child('+v+')').text();
					if(body_tl == '') body_tl = '&nbsp;';
					konten += '<th>'+body_tl+'</th>';
				});
				konten += '</tr></thead>';
				var konten_header = konten;
				konten_header += '</table>';
				konten += '<tbody>';
				var m = 0;
				$('.table-freeze tbody tr').each(function(j,k){
					if(m <= j) {
						konten += '<tr>';
						$.each(e,function(i,v){
							var colspan	= $('table.table-freeze tbody tr:nth-child('+(j+1)+') td:nth-child('+v+')').attr('colspan');
							var rowspan	= $('table.table-freeze tbody tr:nth-child('+(j+1)+') td:nth-child('+v+')').attr('rowspan');
							var cl		= $('table.table-freeze tbody tr:nth-child('+(j+1)+') td:nth-child('+v+')').attr('class');
							var body_tl	= $('table.table-freeze tbody tr:nth-child('+(j+1)+') td:nth-child('+v+')').html();
							var body_h	= $('table.table-freeze tbody tr:nth-child('+(j+1)+') td:nth-child('+v+')').height();
							if($.browser.webkit) {
								body_h += 1;
							}
							if(!colspan && body_tl) {
								var add_cl	= cl ? ' class="bg-white '+cl+'"' : ' class="bg-white"';
								konten += '<td'+add_cl+' style="line-height:'+body_h+'px">'+body_tl+'</th>';
							}
							if(i == 0) {
								if(rowspan && parseInt(rowspan) > 0) {
									m += parseInt(rowspan);
								} else {
									m += 1;
								}
							}
						});
						konten += '</tr>';
					}
				});
				konten += '</tbody>';
				konten += '</table>';
				$('.table-freeze').parent().prepend('<div class="freeze-table body">'+konten+'</div>');
				$('.table-freeze').parent().prepend('<div class="freeze-table body-header">'+konten_header+'</div>');
				var arr	= [];
				var arh = [];
				$('.table-freeze thead th').each(function(i,j){
					arr[i] = [];
					arh[i] = [];
					$.each(e,function(k,l){
						arr[i][k]	= parseFloat($('table.table-freeze thead tr:nth-child(1) th:nth-child('+l+')').width()) + 9;
						arh[i][k]	= parseFloat($('table.table-freeze thead tr:nth-child(1) th:nth-child('+l+')').height());
						if($.browser.webkit) {
							arr[i][k] += 1;
						}
					});
				});
				$('.freeze-table.body table thead th').each(function(i,j){
					$.each(e,function(k,l){
						$('.freeze-table.body table thead tr:nth-child(1) th:nth-child('+l+')').css({'min-width':arr[i][k],'line-height':arh[i][k] + 'px'});
						$('.freeze-table.body-header table thead tr:nth-child(1) th:nth-child('+l+')').css({'min-width':arr[i][k],'line-height':arh[i][k] + 'px'});
					});
				});
			} else {
				if($('[freeze]').length > 0) {
					$('.fixed-content').scrollLeft(0);
					$('.freeze-table.body').remove();
					$('.freeze-table.body-header').remove();
					var konten		= '<table class="'+add_class+'">';
					var col_freeze 	= $('table.table-freeze').attr('col-freeze');
					konten += '<thead></tr>';
					$('.table-freeze thead [freeze]').each(function(i,v){
						var body_tl	= $(this).text();
						var colspan = $(this).attr('colspan');
						var rowspan = $(this).attr('rowspan');
						var height	= $(this).height();
						if(body_tl == '') body_tl = '&nbsp;';
						var add_rowspan= rowspan && parseInt(rowspan) > 0 ? ' rowspan="'+rowspan+'"' : '';
						var add_colspan= colspan && parseInt(colspan) > 0 ? ' colspan="'+colspan+'"' : '';
						konten += '<th'+add_colspan+add_rowspan+' style="line-height:'+height+'px">'+body_tl+'</th>';
					});
					konten += '</tr></thead>';
					var konten_header = konten;
					konten_header += '</table>';
					konten += '<tbody>';
					$('.table-freeze tbody tr').each(function(i,v){
						if($('.table-freeze tbody tr:nth-child('+(i + 1)+') [freeze]').length > 0) {
							konten += '<tr>';
							$('.table-freeze tbody tr:nth-child('+(i + 1)+') [freeze]').each(function(){
								var body_tl	= $(this).text();
								var colspan = $(this).attr('colspan');
								var rowspan = $(this).attr('rowspan');
								var cl 		= $(this).attr('class');
								var height	= $(this).height();
								if($.browser.webkit) {
									height += 1;
								}
								if(body_tl == '') body_tl = '&nbsp;';
								var add_rowspan= rowspan && parseInt(rowspan) > 0 ? ' rowspan="'+rowspan+'"' : '';
								var add_colspan= colspan && parseInt(colspan) > 0 ? ' colspan="'+colspan+'"' : '';
								var add_cl	= cl ? ' class="bg-white '+cl+'"' : ' class="bg-white"';
								konten += '<td'+add_colspan+add_rowspan+add_cl+' style="line-height:'+height+'px">'+body_tl+'</td>';
							})
							konten += '</tr>';
						}
					});
					konten += '</tbody>';
					konten += '</table>';
					$('.table-freeze').parent().prepend('<div class="freeze-table body">'+konten+'</div>');
					$('.table-freeze').parent().prepend('<div class="freeze-table body-header">'+konten_header+'</div>');
					$('.freeze-table.body table thead th').each(function(i,j){
						arr[i]	= parseFloat($('.freeze-table.body table thead tr:nth-child(1) th:nth-child('+(i + 1)+')').width()) + 9;
						if($.browser.webkit) {
							arr[i] += 1;
						}
					});
					$('.freeze-table.body-header table thead th').each(function(i,j){
						$('.freeze-table.body-header table thead tr:nth-child(1) th:nth-child('+(i + 1)+')').css({'min-width':arr[i]});
					});
				}
			}
		}
	}
}
$(document).ready(function(){	
	screen_fix();
	setTimeout(function(){
		screen_fix();		
	},100);
	$('#chk_all').click(function(){
	    if (this.checked) {
	    	$('.chk').each(function(){
	        	$(this).prop('checked', true);
	        	$(this).parent().parent().addClass('checked');
	    	});
	    } else {
	    	$('.chk').each(function(){
	        	$(this).prop('checked', false);
	        	$(this).parent().parent().removeClass('checked');
	    	});
	    }
	});
	$(document).on('click','.chk',function(){
		if(this.checked) {
			$(this).parent().parent().addClass('checked');
		} else {
			$(this).parent().parent().removeClass('checked');
		}
		if ($('.chk:checked').length == $('.chk').length) {
			$('#chk_all').prop('checked',true);
	    } else {
	    	$('#chk_all').prop('checked',false);
	    }
	});
});
$(document).on('keyup keypress blur change click', function(e) {
    activity_life = set_life;
});
$(document).on('mouseenter','.table-freeze.table-hover tbody tr',function(){
	var idx = $(this).index() + 1;
	$('.freeze-table.body table tbody tr:nth-child('+idx+') td').addClass('on-hover');
});
$(document).on('mouseleave','.table-freeze.table-hover tbody tr',function(){
	var idx = $(this).index() + 1;
	$('.freeze-table.body table tbody tr:nth-child('+idx+') td').removeClass('on-hover');
});
$(document).on('mouseenter','.freeze-table.body .table-hover tbody tr',function(){
	var idx = $(this).index() + 1;
	$('.table-freeze.table-hover tbody tr:nth-child('+idx+') td').addClass('on-hover');
});
$(document).on('mouseleave','.freeze-table.body .table-hover tbody tr',function(){
	var idx = $(this).index() + 1;
	$('.table-freeze.table-hover tbody tr:nth-child('+idx+') td').removeClass('on-hover');
});
$('.fixed-content').scroll(function(){
	if($.browser.mozilla || $.browser.webkit) {
		var left = $(this).scrollLeft();
		var top = $(this).scrollTop();
		var freeze_left	= 0 - left;
		var freeze_top	= 0 - top;
		$('.freeze-table.header table').css({'margin-left':freeze_left});
		if($('table.table-freeze[col-freeze]').length > 0) {
			$('.freeze-table.body table').css({'margin-top':freeze_top});
		} else {
			if($('[freeze]').length > 0) {
				$('.freeze-table.body table').css({'margin-top':freeze_top});
			}
		}
	}
});
$(document).on('mousewheel','.freeze-table.body table',function(e,delta){
	if($.browser.mozilla || $.browser.webkit) {
		if(e.deltaX != 0) {
			var sc	= delta * 50;
			$('.fixed-content').scrollLeft( parseInt($('.fixed-content').scrollLeft()) - sc );
		}
		if(e.deltaY != 0) {
			var sc	= delta * 30;
			$('.fixed-content').scrollTop( parseInt($('.fixed-content').scrollTop()) - sc );		
		}
	}
});
$(window).on('resize', function(){
	screen_fix();
	if($.browser.mozilla || $.browser.webkit) {
		if($('table.table-freeze').length == 1) {
			var arr	= [];
			$('.table-freeze thead th').each(function(i,j){
				arr[i]	= parseFloat($(this).width()) + 9;
				if($.browser.webkit) {
					arr[i] += 1;
				}
			});
			$('.freeze-table.header table thead th').each(function(i,j){
				$(this).css({'min-width':arr[i]});
			});
		}
		if($('table.table-freeze[col-freeze]').length > 0) {
			var col_freeze 	= $('table.table-freeze').attr('col-freeze');
			var e			= col_freeze.split(',');
			var arr	= [];
			var arh = [];
			$('.table-freeze thead th').each(function(i,j){
				arr[i] = [];
				arh[i] = [];
				$.each(e,function(k,l){
					arr[i][k]	= parseFloat($('table.table-freeze thead tr:nth-child(1) th:nth-child('+l+')').width()) + 9;
					arh[i][k]	= parseFloat($('table.table-freeze thead tr:nth-child(1) th:nth-child('+l+')').height());
					if($.browser.webkit) {
						arr[i][k] += 1;
					}
				});
			});
			$('.freeze-table.body table thead th').each(function(i,j){
				$.each(e,function(k,l){
					$('.freeze-table.body table thead tr:nth-child(1) th:nth-child('+l+')').css({'min-width':arr[i][k],'line-height':arh[i][k] + 'px'});
					$('.freeze-table.body-header table thead tr:nth-child(1) th:nth-child('+l+')').css({'min-width':arr[i][k],'line-height':arh[i][k] + 'px'});
				});
			});
		}
	}
});
$('.dropdown-language a').click(function(){
	var language = $(this).attr('data-value');
	$.ajax({
		url		: 'home/change_language',
		data	: {'language':language},
		type	: 'post',
		success	: function(response) {
			location.reload();
		}
	});
});