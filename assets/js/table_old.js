var last_width = 0;
var has_fixed = false;
var timeout = null;
var last_serialize = {};
var left_fixed = 0;
var freeze_count = 0;
var refixed = false;
var cek_kode = false;
(function($){
	$.fn.serverside = function(options) {

		var defaults = {
			rangeShow : [25,50,75,100],
			highlight: true
		}

		var settings = $.extend({}, defaults, options);
		var xhr_serverside = null;
		var proccess = false;

		this.each(function(){
			var $t = $(this);
			if($t.find('th[data-content]').length > 0) {
				var filter = '<tr>';
				$t.find('th[data-content]').each(function(){
					if($(this).attr('data-content') == 'action_button') {
						$(this).attr('data-action','true');
						$(this).removeAttr('data-content')
					}
					if(typeof $(this).attr('data-content') != 'undefined') {
						if(typeof $(this).attr('data-table') == 'undefined') {
							$(this).attr('data-field', $t.attr('data-table') + '.' + $(this).attr('data-content'));
							$(this).attr('data-alias', $t.attr('data-table').replace(' ','_') + '_' + $(this).attr('data-content'));
							$(this).removeAttr('data-content');
						} else {
							$(this).attr('data-field', $(this).attr('data-table') + '.' + $(this).attr('data-content'));
							$(this).attr('data-alias', $(this).attr('data-table').replace(' ','_') + '_' + $(this).attr('data-content'));
							$(this).removeAttr('data-content').removeAttr('data-table');
						}
					}
					if(typeof $(this).attr('data-filter') == 'undefined' && typeof $(this).attr('data-alias') != 'undefined' && $(this).attr('data-field') != $t.attr('data-table') + '.id') {
						var ff = $(this).attr('data-field').split(' ');
						var f = ff.length == 2 ? ff[1] : ff[0];
						if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'boolean') {
							filter += '<th><select class="custom-select select-boolean" data-filter="'+f+'" style="min-width:90px"><option value=""></option><option value="1">TRUE</option><option value="0">FALSE</option></select></th>';
						} else if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'date') {
							filter += '<th><input type="text" class="form-control dp-table" data-filter="'+f+'"></th>';
						} else if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'daterange') {
							filter += '<th><input type="text" class="form-control drp-table" data-filter="'+f+'"></th>';
						} else if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'image') {
							filter += '<th>&nbsp;</th>';
						} else {
							if(typeof $(this).attr('data-replace') != 'undefined') {
								filter += '<th><select class="custom-select select-replace" data-filter="'+f+'" style="min-width:90px"><option value=""></option>';
								var rpl = $(this).attr('data-replace').split('|');
								$.each(rpl,function(xd,xv){
									var xxv = xv.split(':');
									if(xxv.length == 2) {
										filter += '<option value="'+xxv[0]+'">'+xxv[1]+'</option>';
									}
								});
								filter += '</select></th>';
							} else {
								filter += '<th><input type="text" class="form-control" data-filter="'+f+'"></th>';
							}
						}
					} else {
						if($(this).attr('data-field') == $t.attr('data-table') + '.id' && $(this).find('[type="checkbox"]').length == 1) {
							filter += '<th class="align-middle">'+$(this).html()+'</th>';
							$(this).html('&nbsp;');
						} else {
							filter += '<th>&nbsp;</th>';
						}
						$(this).removeAttr('data-filter');
					}
					if(typeof $(this).attr('data-sort') == 'undefined' && typeof $(this).attr('data-alias') != 'undefined' && $(this).attr('data-field') != $t.attr('data-table') + '.id') {
						if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'image') {
							$(this).html($(this).text());
							$(this).removeAttr('data-sort');
						} else {
							$(this).html('<span data-sort="both">'+$(this).text()+'</span>');
							$(this).attr('data-sortable',true);
						}
					} else {
						$(this).removeAttr('data-sort');
					}
				});
				filter += '</tr>';
				$($t.children('thead').append(filter));
			}
			if($t.find('tbody').length == 0) {
				$t.append('<tbody></tbody>');
			}
			$t.parent().addClass('content-serverside');
			if(typeof $t.attr('data-fixed') == 'undefined') {
				$t.parent().addClass('nonfixed');
			}
			var serverside_footer = '<div class="footer-serverside">';
			serverside_footer += '<div class="main-container">';
			serverside_footer += '<div class="row">';
			serverside_footer += '<div class="col-8">';
			serverside_footer += '<div class="form-inline">';
			serverside_footer += '<label class="mr-2">Tampilkan</label>';
			serverside_footer += '<select class="custom-select" data-show="true">';
			$.each(settings.rangeShow,function(e,d){
				serverside_footer += '<option value="'+d+'">'+d+'</option>';
			});
			serverside_footer += '</select>';
			serverside_footer += '<span data-info="true"></span>';
			serverside_footer += '</div>';
			serverside_footer += '</div>';
			serverside_footer += '<div class="col-4 text-right">';
			serverside_footer += '<div class="form-inline form-pagination">';
			serverside_footer += '<button type="button" data-page="prev">&lsaquo;</button>';
			serverside_footer += '<select class="custom-select" data-page="page">';
			serverside_footer += '</select>';
			serverside_footer += '<button type="button" data-page="next">&rsaquo;</button>';
			serverside_footer += '</div>';
			serverside_footer += '</div>';
			serverside_footer += '</div>';
			serverside_footer += '</div>';
			serverside_footer += '</div>';
			serverside_footer += '</div>';
			$(serverside_footer).insertAfter('[data-serverside]');
			$('.footer-serverside select[data-show]').select2({
				minimumResultsForSearch: Infinity,
				width: 'resolve'
			});
			$('.footer-serverside select[data-page]').select2({
				width: 'resolve'
			});

			getData($t, false, true);
			$(document).on('change','[data-serverside] select[data-filter]',function(){
				getData($t);
			});
			$(document).on('keyup','[data-serverside] input[data-filter]',function(){
				clearTimeout(timeout);
				timeout = setTimeout(function(){
					getData($t);
				},100);
			});
			$('.footer-serverside select[data-page], .footer-serverside select[data-show]').change(function(){
				if(!proccess) {
					getData($t, false, true);
				}
			});
			$(document).on('change','[data-serverside] .dp-table, [data-serverside] .drp-table',function(){
				if(!proccess) {
					getData($t);
				}
			});
			$('.footer-serverside [data-page="next"]').click(function(){
				var nextPage = parseInt($('.footer-serverside select[data-page]').val()) + 1;
				if($('.footer-serverside select[data-page]').find('option[value="'+nextPage+'"]').length == 1) {
					$('.footer-serverside select[data-page]').val(nextPage).trigger('change');
				}
			});
			$('.footer-serverside [data-page="prev"]').click(function(){
				var prevPage = parseInt($('.footer-serverside select[data-page]').val()) - 1;
				if($('.footer-serverside select[data-page]').find('option[value="'+prevPage+'"]').length == 1) {
					$('.footer-serverside select[data-page]').val(prevPage).trigger('change');
				}
			});
			$(document).on('click','[data-serverside] [data-sortable]',function(){
				var cur_sort 	= $(this).children('[data-sort]').attr('data-sort');
				var next_sort	= '';
				if(cur_sort == 'both') next_sort = 'asc';
				else if(cur_sort == 'asc') next_sort = 'desc';
				else if(cur_sort == 'desc') next_sort = 'both';
				$('[data-serverside] [data-sort]').attr('data-sort','both');
				$(this).children('[data-sort]').attr('data-sort',next_sort);
				getData($t);
			});
			$(document).on('click','[data-serverside] thead th input[type="checkbox"]',function(){
				if($(this).is(':checked')) {
					$('[data-serverside] tbody td input[type="checkbox"]').each(function(){
						$(this).prop('checked',true);
						$(this).closest('tr').addClass('checked');
						if($('.fixed-table.body').length == 1) {
							var index = $(this).closest('tr').index() + 1;
							$('.fixed-table.body tr:nth-child('+index+')').find('input[type="checkbox"]').prop('checked',true);
							$('.fixed-table.body tr:nth-child('+index+')').addClass('checked');
						}
					});
				} else {
					$('[data-serverside] tbody td input[type="checkbox"]').each(function(){
						$(this).prop('checked',false);
						$(this).closest('tr').removeClass('checked');
						if($('.fixed-table.body').length == 1) {
							var index = $(this).closest('tr').index() + 1;
							$('.fixed-table.body tr:nth-child('+index+')').find('input[type="checkbox"]').prop('checked',false);
							$('.fixed-table.body tr:nth-child('+index+')').removeClass('checked');
						}
					});
				}
			});
			$(document).on('click','[data-serverside] tbody td input[type="checkbox"]',function(){
				var tr = $(this).closest('tr');
				var index = tr.index() + 1;
				if($(this).is(':checked')) {
					tr.addClass('checked');
					$('.fixed-table.body tr:nth-child('+index+')').addClass('checked');
				} else {
					tr.removeClass('checked');
					$('.fixed-table.body tr:nth-child('+index+')').removeClass('checked');
				}
				if($('[data-serverside] tbody td input[type="checkbox"]:checked').length > 0) {
					if($('[data-serverside] tbody td input[type="checkbox"]:checked').length == $('[data-serverside] tbody td input[type="checkbox"]').length) {
						$('[data-serverside] thead th input[type="checkbox"], .fixed-table.header thead th input[type="checkbox"], .fixed-table.header2 thead th input[type="checkbox"]').prop('indeterminate',false);
						$('[data-serverside] thead th input[type="checkbox"], .fixed-table.header thead th input[type="checkbox"], .fixed-table.header2 thead th input[type="checkbox"]').prop('checked',true);
					} else {
						$('[data-serverside] thead th input[type="checkbox"], .fixed-table.header thead th input[type="checkbox"], .fixed-table.header2 thead th input[type="checkbox"]').prop('indeterminate',true);
						$('[data-serverside] thead th input[type="checkbox"], .fixed-table.header thead th input[type="checkbox"], .fixed-table.header2 thead th input[type="checkbox"]').prop('checked',false);
					}
				} else {
					$('[data-serverside] thead th input[type="checkbox"], .fixed-table.header thead th input[type="checkbox"], .fixed-table.header2 thead th input[type="checkbox"]').prop('indeterminate',false);
					$('[data-serverside] thead th input[type="checkbox"], .fixed-table.header thead th input[type="checkbox"], .fixed-table.header2 thead th input[type="checkbox"]').prop('checked',false);
				}
			});

			$(document).on('dblclick','[data-serverside] tbody td .badge',function(){
				if($(this).closest('tr').find('.btn-input').length == 1) {
					var badge_status 	= '0';
					var data_id 		= $(this).closest('tr').find('.btn-input').attr('data-id');
					var index 			= $(this).closest('td').index() + 1;
					var fth_change		= $('[data-serverside] thead tr:nth-child(1) th:nth-child('+index+')').attr('data-field');
					var xfth_change		= fth_change.split('.');
					var field_change	= xfth_change[1];
					if( $(this).hasClass('badge-danger') ) {
						badge_status = '1';
					}
					active_inactive(data_id,badge_status,field_change);
				}
			});

			
			if($('.nonfixed .dp-table').length > 0) {
				$('.nonfixed .dp-table').daterangepicker({
					singleDatePicker: true,
					showDropdowns: true,
					minYear: 1950,
					maxYear: parseInt(moment().format('YYYY'),10) + 3,
					locale: {
						format: 'DD/MM/YYYY'
					},
					autoUpdateInput: false
				}).on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format('DD/MM/YYYY'));
				}).mask('00/00/0000', {
					placeholder: "dd/mm/yyyy"
				}).change(function(e){
					var $t = $(this);
					var field = $t.attr('data-filter');
					var text = $t.val();
					if(text.length == 10) {
						$('[data-serverside] input[data-filter="'+field+'"]').val(text).trigger('change');
					} else {
						$('[data-serverside] input[data-filter="'+field+'"]').val('').trigger('change');
						$t.val('');
					}
				});
			}
			if($('.nonfixed .drp-table').length > 0) {
				$('.nonfixed .drp-table').daterangepicker({
					opens: 'left',
					showDropdowns: true,
					minYear: 1950,
					maxYear: parseInt(moment().format('YYYY'),10) + 3,
					locale: {
						format: 'DD/MM/YYYY',
						cancelLabel: 'Clear'
					},
					autoUpdateInput: false
				}).on('apply.daterangepicker', function(ev, picker) {
					var $t = $(this);
					var text = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY');
					if($t.val() != text){
						var field = $t.attr('data-filter');
						$t.val(text);
						$('[data-serverside] input[data-filter="'+field+'"]').val(text).trigger('change');
					}
				}).on('cancel.daterangepicker', function(ev, picker) {
					var $t = $(this);
					var field = $t.attr('data-filter');
					var text = '';
					$t.val(text);
					$('[data-serverside] input[data-filter="'+field+'"]').val(text);
					$('[data-serverside] input[data-filter="'+field+'"]').trigger('change');
				}).mask('00/00/0000 - 00/00/0000', {
					placeholder: "dd/mm/yyyy - dd/mm/yyyy"
				}).change(function(e){				
					var $t = $(this);
					var field = $t.attr('data-filter');
					var text = $t.val();
					if(text.length == 23) {
						$('[data-serverside] input[data-filter="'+field+'"]').val(text).trigger('change');
					} else {
						$('[data-serverside] input[data-filter="'+field+'"]').val('').trigger('change');
						$t.val('');
					}
				});
			}
			if(typeof $('[data-serverside]').attr('data-fixed') == 'undefined') {
				setTimeout(function(){
					$('.footer-serverside').css({'width':$('[data-serverside]').outerWidth()});
				},500);
			}
		});

		return {
			refresh: function() {
				getData($('[data-serverside]'),true);
			}
		}

		function actionChk() {
			var $tId = $('[data-serverside]').attr('id');
			$('#'+$tId+' .sub-chk').click(function(){
				if($('#'+$tId+' .sub-chk:checked').length > 0) {
					if($('#'+$tId+' .sub-chk:checked').length == $('#'+$tId+' .sub-chk').length) {
						$('#'+$tId+' .chk-all').prop('indeterminate',false);
						$('#'+$tId+' .chk-all').prop('checked',true);
					} else {
						$('#'+$tId+' .chk-all').prop('indeterminate',true);
						$('#'+$tId+' .chk-all').prop('checked',false);
					}
				} else {
					$('#'+$tId+' .chk-all').prop('indeterminate',false);
					$('#'+$tId+' .chk-all').prop('checked',false);
				}
			});
		}

		function numberFormat(e, c, d, t){
			var n = e, 
			c = isNaN(c = Math.abs(c)) ? 2 : c, 
			d = d == undefined ? "." : d, 
			t = t == undefined ? "," : t, 
			s = n < 0 ? "-" : "", 
			i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
			j = (j = i.length) > 3 ? j % 3 : 0;
			var result = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
			if(s == '-') {
				result = '(' + result.replace('-','') + ')';
			}
			return result;
		};

		function getData(e,refresh,refix) {
			if(typeof refresh == 'undefined') var refresh = false;
			if(typeof refix == 'undefined') var refix = false;
			proccess		= true;
			var field 		= [];
			var alias 		= [];
			var filter 		= [];
			var filter_v 	= [];
			var order_by	= '';
			var order_type	= '';
			var item_act	= {};
			$('[data-serverside] [data-sortable]').each(function(){
				if($(this).children('span').attr('data-sort') !== 'both') {
					order_by	= $(this).attr('data-alias');
					order_type	= $(this).children('span').attr('data-sort');
				}
			});
			e.find('[data-field]').each(function(i,d){
				field.push($(this).attr('data-field'));
				alias.push($(this).attr('data-alias'));
			});
			if(typeof $('[data-serverside]').attr('data-additional-select') != 'undefined') {
				var additional_select = $('[data-serverside]').attr('data-additional-select');
				var s_additional = additional_select.split(',');
				$.each(s_additional,function(e_add,d_add){
					if(d_add.indexOf('.') == -1) {
						field.push($('[data-serverside]').attr('data-table')+'.'+d_add);
						alias.push($('[data-serverside]').attr('data-table')+'_'+d_add);
					} else {
						field.push(d_add);
						alias.push(d_add.replace('.','_'));
					}
				});
			}
			e.find('[data-filter]').each(function(i,d){
				var v = $(this).val() ? $(this).val() : '';
				if(v != '') {
					filter.push($(this).attr('data-filter'));
					filter_v.push(v);
				}
			});
			var serialize = {};
			serialize['field'] 		= field;
			serialize['alias'] 		= alias;
			serialize['filter']		= filter;
			serialize['f_val']		= filter_v;
			serialize['table']		= e.attr('data-table');
			serialize['limit']		= $('[data-show]').val();
			serialize['offset']		= isNaN(parseInt($('[data-page="page"]').val())) ? 0 : (parseInt($('[data-page="page"]').val()) - 1) * parseInt(serialize['limit']);
			serialize['order_by']	= order_by;
			serialize['order_type']	= order_type;
			var p_data				= false;
			if(JSON.stringify(last_serialize) == JSON.stringify(serialize)) {
				p_data = false;
			} else {
				p_data = true;
			}
			if(p_data || refresh) {
				var curPage 			= isNaN(parseInt($('[data-page="page"]').val())) ? 1 : parseInt($('[data-page="page"]').val());
				$('.footer-serverside [data-info]').addClass('waiting').text('Memuat Data...');
				if( xhr_serverside != null ) {
					xhr_serverside.abort();
					xhr_serverside = null;
				}
				xhr_serverside = $.ajax({
					url		: e.attr('data-serverside'),
					data 	: serialize,
					type 	: 'post',
					dataType : 'json',
					cache	: false,
					success	: function(res) {						
						last_serialize = serialize;
						$('.footer-serverside select[data-page]').html('');
						$('.footer-serverside [data-info]').removeClass('waiting').text('');
						if(res.status == 'success') {
							var f_show		= serialize['offset'] + 1;
							var konten 		= '';
							var jmlLimit	= typeof res.jmlFilter == 'undefined' ? res.jmlAll : res.jmlFilter;
							var jmlPage 	= Math.ceil(parseInt(jmlLimit)/parseInt(serialize['limit']));
							var tmpPage		= curPage;
							if(parseInt(jmlLimit) > 0 && jmlPage < curPage) {
								tmpPage 	= jmlPage;
							}
							if(jmlPage < 500) {
								for(var o = 1; o <= jmlPage; o++) {
									$('.footer-serverside select[data-page]').append('<option value="'+o+'">'+o+'</option>');
								}
							} else {
								var startPage	= tmpPage - 100;
								var endPage 	= tmpPage + 100;
								if(startPage < 1) startPage = 1;
								if(endPage > jmlPage) endPage = jmlPage;
								for(var o = startPage; o <= endPage; o++) {
									$('.footer-serverside select[data-page]').append('<option value="'+o+'">'+o+'</option>');
								}
							}
							if(parseInt(res.jmlShow) == 0) {
								if(jmlLimit == 0) {
									var colspan = parseFloat(e.children('thead').children('tr').first().children().length);
									if(typeof res.jmlFilter == 'undefined') {
										konten = '<tr class="not-hover"><td colspan="'+colspan+'" class="result-error"><i class="fa-info-circle error-icon"></i>Tidak ada data</td></tr>';
										$('.footer-serverside [data-info]').text('Tidak ada data');
									} else {
										konten = '<tr class="not-hover"><td colspan="'+colspan+'" class="result-error"><i class="fa-search error-icon"></i>Tidak ditemukan data cocok dari pencarian</td></tr>';								
										$('.footer-serverside [data-info]').text('Tidak ada data yang cocok');
									}
								} else {
									proccess = false;
									$('.footer-serverside select[data-page]').val(jmlPage).trigger('change');
									return;
								}
								$('.fixed-table.body tbody').html('');
							} else {
								if(parseInt(jmlLimit) > 0 && jmlPage < curPage) {
									proccess = false;
									$('.footer-serverside select[data-page]').val(jmlPage).trigger('change');
									return;
								} else {
									$('.footer-serverside select[data-page]').val(curPage).trigger('change');
									$.each(res.data,function(i,d){
										konten += '<tr>';
										e.children('thead').children('tr').first().children().each(function(){
											var cls = "";
											if(typeof $(this).attr('class') != 'undefined') cls = ' class="'+$(this).attr('class')+'"';
											if(typeof $(this).attr('data-alias') != 'undefined') {
												if(typeof res.data[i][$(this).attr('data-alias')] != 'undefined') {
													var f_name = $(this).attr('data-field');
													var e_name = f_name.split('.');
													var string = res.data[i][$(this).attr('data-alias')];
													var str_label = string;
													if(string == null) string = '';
													if(string == '0000-00-00' || string == '0000-00-00 00:00:00') string = '';
													if(e_name[1] == 'id') {
														if(e.children('thead').children('tr').last().find('input[type="checkbox"]').length == 1) {
															konten += '<td'+cls+'>';
															konten += '<div class="custom-checkbox custom-control">';
															konten += '<input class="custom-control-input" type="checkbox" id="chk-td-'+i+'" value="'+res.data[i][$('[data-serverside]').attr('data-table') + '_id']+'">';
															konten += '<label class="custom-control-label" for="chk-td-'+i+'">&nbsp;</label>';
															konten += '</div>';
															konten += '</td>';
														} else {
															var i_show = i + f_show;
															if(i_show && typeof $(this).attr('data-prefix') != 'undefined') {
																i_show = $(this).attr('data-prefix') + ' ' + i_show;
															}
															if(i_show && typeof $(this).attr('data-suffix') != 'undefined') {
																i_show += ' ' + $(this).attr('data-suffix');
															}
															if(i_show && typeof $(this).attr('data-link') != 'undefined') {
																var hashids_link = new Hashids(encode_key);
																var encode_id = hashids_link.encode(res.data[i][$('[data-serverside]').attr('data-table') + '_id'], Math.floor(Math.random() * (9999 - 1000 + 1)) + 100 );
																var link_col = $(this).attr('data-link');
																if(link_col.substr(link_col.length - 1) != '/') {
																	link_col += '/';
																}
																i_show = '<a href="'+base_url+link_col+encode_id+'">'+i_show+'</a>';
															}
															if(cls) {
																cls = cls.substr(0, cls.length - 1) + ' text-nowrap"';
															} else {
																cls += ' class="text-nowrap"';
															}
															konten += '<td'+cls+'>'+i_show+'</td>';
														}
													} else {
														if(typeof $(this).attr('data-replace') != 'undefined') {
															var rpl = $(this).attr('data-replace').split('|');
															$.each(rpl,function(xd,xv){
																var xxv = xv.split(':');
																if(xxv.length == 2 && xxv[0] == string) {
																	string = xxv[1];
																}
															});
															str_label = string;
														} else {
															if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'boolean') {
																if(string == '1') {
																	string = '<span class="badge badge-success">TRUE</span>';
																	str_label = 'TRUE';
																} else {
																	string = '<span class="badge badge-danger">FALSE</span>';
																	str_label = 'FALSE';
																}
															} else if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'currency') {
																if(typeof $(this).attr('data-absolute') != 'undefined'){
																	string = string.replace('-','');
																}
																str_label = numberFormat(parseFloat(string), 0, ',', '.');
																if(parseFloat(string) < 0) {
																	string = '<span class="currency-negative">' + numberFormat(parseFloat(string), 0, ',', '.') + '<span>';
																} else {
																	string = numberFormat(parseFloat(string), 0, ',', '.');
																}
															} else if(typeof $(this).attr('data-type') != 'undefined' && ($(this).attr('data-type') == 'date' || $(this).attr('data-type') == 'daterange')) {
																var rm_time 	= string.split(' ');
																var check_date 	= rm_time[0].split('-');
																if(check_date.length == 3) {
																	string = check_date[2] + '/' + check_date[1] + '/' + check_date[0];
																	if(typeof rm_time[1] != 'undefined' && rm_time[1].length == 8) {
																		var check_time = rm_time[1].split(':');
																		if(check_time.length == 3) {
																			string += ' ' + check_time[0] + ':' + check_time[1];
																		}
																	}
																}
																str_label = string;
															} else if(typeof $(this).attr('data-type') != 'undefined' && $(this).attr('data-type') == 'image') {
																var image = string == '' ? 'default.png' : string;
																string = '<img src="'+res['dirUpload']+image+'" class="img-thumbnail" alt="" />';
																str_label = 'Image';
															} else {
																var ff = $(this).attr('data-field').split(' ');
																var xf = ff.length == 2 ? ff[1] : ff[0];
																if(e.find('input[data-filter="'+xf+'"]').length == 1) {
																	var str = '';
																	if(settings.highlight == true) {
																		var str_rep = e.find('input[data-filter="'+xf+'"]').val();
																		reg = new RegExp(str_rep, 'gi');
																		if(string.length > 0) {
																			str = string.replace(reg, function(st) {return '<span class="highlight">'+st+'</span>'});
																		}
																		string = str;
																	}
																}
															}
														}
														if(string && typeof $(this).attr('data-prefix') != 'undefined') {
															string = $(this).attr('data-prefix') + ' ' + string;
														}
														if(string && typeof $(this).attr('data-suffix') != 'undefined') {
															string += ' ' + $(this).attr('data-suffix');
														}
														if(string && typeof $(this).attr('data-icon') != 'undefined') {
															var icons = $(this).attr('data-icon');
															var _icon = icons.split('#');
															var icon = _icon[0];
															var field_icon = $(this).attr('data-alias');
															if(typeof _icon[1] != 'undefined') {
																var _field = _icon[1].split('.');
																if(_field.length == 1) {
																	field_icon = $('[data-serverside]').attr('data-table') + '_' + _icon[1];
																} else {
																	field_icon = _icon[1].replace('.','_');
																}
															}
															var s_icon = icon.split('|');
															if(s_icon.length == 1) {
																var s_def_icon = icon.split(':');
																if(s_def_icon.length == 2) {
																	if(res.data[i][field_icon] == s_def_icon[0]) {
																		string = '<i class="'+s_def_icon[1]+' col-icon"></i> ' + string;
																	}
																} else {
																	string = '<i class="'+icon+' col-icon"></i> ' + string;
																}
															} else {
																$.each(s_icon,function(e_icon,d_icon){
																	var s_def_icon = d_icon.split(':');
																	if(s_def_icon.length == 2) {
																		if(res.data[i][field_icon] == s_def_icon[0]) {
																			string = '<i class="'+s_def_icon[1]+' col-icon"></i> ' + string;
																		}
																	}
																});
															}
															if(cls) {
																cls = cls.substr(0, cls.length - 1) + ' text-nowrap"';
															} else {
																cls += ' class="text-nowrap"';
															}
														}
														if(string && typeof $(this).attr('data-badge') != 'undefined') {
															var badge = $(this).attr('data-badge');
															var field_badge = res.data[i][$(this).attr('data-alias')];
															if(badge) {
																var _field = badge.split('.');
																var field_b = '';
																if(_field.length == 1) {
																	field_b = $('[data-serverside]').attr('data-table') + '_' + badge;
																} else {
																	field_b = badge.replace('.','_');
																}
																field_badge = res.data[i][field_b];
															}
															string = '<span class="icon-badge" style="background: '+field_badge+'"></span> ' + string;
															if(cls) {
																cls = cls.substr(0, cls.length - 1) + ' text-nowrap"';
															} else {
																cls += ' class="text-nowrap"';
															}
														}
														if(string && typeof $(this).attr('data-link') != 'undefined') {
															var hashids_link = new Hashids(encode_key);
															var encode_id = hashids_link.encode(res.data[i][$('[data-serverside]').attr('data-table') + '_id'], Math.floor(Math.random() * (9999 - 1000 + 1)) + 100 );
															var link_col = $(this).attr('data-link');
															if(link_col.substr(link_col.length - 1) != '/') {
																link_col += '/';
															}
															var link_target = '';
															if(typeof $(this).attr('data-link-target') != 'undefined') {
																link_target = ' target="' + $(this).attr('data-link-target') + '"';
															}
															string = '<a href="'+base_url+link_col+encode_id+'"'+link_target+'>'+string+'</a>';
														}
														var tdWidth = $(this).css('max-width');
														if(tdWidth == 'none') {
															konten += '<td'+cls+' style="max-width:'+$(this).outerWidth()+'px" title="'+str_label+'">'+string+'</td>';
														} else {
															konten += '<td'+cls+' style="max-width:'+tdWidth+'" title="'+str_label+'">'+string+'</td>';
														}
													}
												} else {
													konten += '<td'+cls+'>???</td>';
												}
											} else {
												if(typeof $(this).attr('data-action') == 'undefined') {
													konten += '<td'+cls+'></td>';
												} else {
													konten += '<td class="button">';
													var hashids = new Hashids(encode_key);
													var encode_id = hashids.encode(res.data[i][$('[data-serverside]').attr('data-table') + '_id'], Math.floor(Math.random() * (9999 - 1000 + 1)) + 100 );
													if(typeof res.additionalButton != 'undefined') {
														$.each(res.additionalButton,function(key,value){
															var decode 	= Base64.decode(res.additionalButton[key]);
															var d_res	= decode.split('>>');
															var c_icon 	= '';
															var show_button = false;
															if(d_res.length == 5) {
																var icon = '<i class="fa-eye"></i>';
																if(d_res[2] != '') {
																	var c_arr = d_res[2].split('<<');
																	if(c_arr.length == 1) {
																		var c_icon = d_res[2].split('fa-');
																		if(c_icon.length == 2) {
																			icon = '<i class="'+d_res[2]+'"></i>';
																			c_icon = d_res[2].replace('fa-','');
																		} else {
																			icon = d_res[2].charAt(0).toUpperCase() + d_res[2].slice(1);
																		}
																	} else {
																		c_icon = c_arr[0].replace('fa-','');
																		if(c_arr.length == 2) {
																			icon = '<i class="'+c_arr[0]+'"></i> ' + c_arr[1].charAt(0).toUpperCase() + c_arr[1].slice(1);
																		} else {
																			icon = '<i class="'+c_arr[0]+'"></i> <span class="hidden">' + c_arr[1].charAt(0).toUpperCase() + c_arr[1].slice(1) + '</span>';
																		}
																	}
																}
																if(d_res[4] == '') show_button = true;
																else {
																	show_button = true;
																	var conds = d_res[4].split('&&');
																	$.each(conds,function(y,z){
																		var cond = conds[y].split('<<');
																		var cond_attr = cond[1].split('||');
																		var con = cond[0].split(' ');
																		var s_button = false;
																		var f_con = con[0];
																		if(typeof res.data[i][f_con] == 'undefined') {
																			$('[data-table]').each(function(){
																				var t_con = $(this).attr('data-table') + '_' + f_con;
																				if(typeof res.data[i][t_con] != 'undefined') {
																					f_con = t_con;
																				}
																			});
																		}
																		if(con.length == 1) {
																			$.each(cond_attr,function(w,y){
																				if(res.data[i][f_con] == cond_attr[w] && s_button == false) {
																					s_button = true;
																				}
																			});
																		} else {
																			$.each(cond_attr,function(w,y){
																				if(con[1] == '=' && res.data[i][con[0]] == cond_attr[w] && s_button == false) {
																					s_button = true;
																				} else if(con[1].trim() == '!=' && res.data[i][f_con] != cond_attr[w] && s_button == false) {
																					s_button = true;
																				} else if(con[1].trim() == '<>' && res.data[i][f_con] != cond_attr[w] && s_button == false) {
																					s_button = true;
																				} else if(con[1].trim() == '><' && res.data[i][f_con] != cond_attr[w] && s_button == false) {
																					s_button = true;
																				} else if(con[1].trim() == '>' && parseFloat(res.data[i][f_con]) > parseFloat(cond_attr[w]) && s_button == false) {
																					s_button = true;
																				} else if(con[1].trim() == '>=' && parseFloat(res.data[i][f_con]) >= parseFloat(cond_attr[w]) && s_button == false) {
																					s_button = true;
																				} else if(con[1].trim() == '<' && parseFloat(res.data[i][f_con]) < parseFloat(cond_attr[w]) && s_button == false) {
																					s_button = true;
																				} else if(con[1].trim() == '<=' && parseFloat(res.data[i][f_con]) <= parseFloat(cond_attr[w]) && s_button == false) {
																					s_button = true;
																				}
																			});
																		}
																		if(s_button == false || show_button == false) {
																			show_button = false;
																		}
																	});
																}

																if(show_button) {
																	if(d_res[1].split('/').length > 1) {
																		if(d_res[3] != 'cInfo') {
																			konten += '<a href="'+d_res[1] + encode_id +'" data-key="'+d_res[3]+'" class="btn '+d_res[0]+'" title="'+icon.replace(/(<([^>]+)>)/ig,"").trim('')+'">'+icon+'</a>'  + "\n";
																		} else {
																			konten += '<button data-target="'+d_res[1] + encode_id +'" data-key="'+d_res[3]+'" class="btn '+d_res[0]+' cInfo" title="'+icon.replace(/(<([^>]+)>)/ig,"").trim('')+'">'+icon+'</button>'  + "\n";
																		}
																	} else {
																		var clr = d_res[1].split('.').join("");
																		var act = clr.split('#').join("");
																		konten += '<button type="button" data-key="'+d_res[3]+'" class="btn '+d_res[0]+' '+act+'" data-id="'+res.data[i][$('[data-serverside]').attr('data-table') + '_id']+'" title="'+icon.replace(/(<([^>]+)>)/ig,"").trim('')+'">'+icon+'</button>'  + "\n";
																	}
																	if(typeof item_act[d_res[3]] == 'undefined') {
																		var icon_text = icon.replace(/(<([^>]+)>)/ig,"");
																		if(icon_text == '') icon_text = 'Detail';
																		if(c_icon != '') {
																			item_act[d_res[3]] = {name : icon_text, icon : c_icon};
																		} else {
																			item_act[d_res[3]] = {name : icon_text};
																		}
																	}
																}
															}
														});
													}
													if(res.accessEdit == '1' || res.accessDelete == '1' || res.accessView == '1') {
														var act_count = 0;
														for (var c in item_act) {
															act_count = act_count + 1;
														}
														if(act_count > 0 && (typeof item_act['edit'] == 'undefined' || typeof item_act['delete'] == 'undefined')) {
															item_act["sep1"] = "---------";
														}
														if($('.btn-act-active').length == 2) {
															item_act['active'] 		= {name : "Aktiv", icon : "toggle-on"};
															item_act['inactive'] 	= {name : "Non Aktiv", icon : "toggle-off"};
															item_act["sep2"] = "---------";
														}
														if(res.accessView == '1') {
															if(typeof item_act['view'] == 'undefined') {
																item_act['view'] = {name : "Detail", icon : "search"};
															}
															if(res.linkView == '') {
																konten += '<button type="button" class="btn btn-info btn-act-view" data-key="view" data-id="'+res.data[i][$('[data-serverside]').attr('data-table') + '_id']+'" title="Detail"><i class="fa-search"></i></button>' + "\n";
															} else {
																konten += '<a href="'+res.linkView+encode_id+'" class="btn btn-info" data-key="view" title="Detail"><i class="fa-search"></i></a>' + "\n";
															}
														}
														if(res.accessEdit == '1' && (typeof $('[data-serverside] th[data-action]').attr('data-edit') == 'undefined' || (typeof $('[data-serverside] th[data-action]').attr('data-edit') !== 'undefined' && typeof $('[data-serverside] th[data-action]').attr('data-edit') == 'true')) ) {
															if(typeof item_act['edit'] == 'undefined') {
																item_act['edit'] = {name : "Edit", icon : "edit"};
															}
															if(res.linkEdit == '') {
																konten += '<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="'+res.data[i][$('[data-serverside]').attr('data-table') + '_id']+'" title="Edit"><i class="fa-edit"></i></button>' + "\n";
															} else {
																konten += '<a href="'+res.linkEdit+encode_id+'" class="btn btn-warning" data-key="edit" title="Edit"><i class="fa-edit"></i></a>' + "\n";
															}
														}
														if(res.accessDelete == '1') {
															if(typeof item_act['delete'] == 'undefined') {
																item_act['delete'] = {name : "Hapus", icon : "delete"};
															}
															if(res.linkDelete == '') {
																konten += '<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="'+res.data[i][$('[data-serverside]').attr('data-table') + '_id']+'" title="Hapus"><i class="fa-trash-alt"></i></button>';
															} else {
																konten += '<a href="'+res.linkDelete+encode_id+'" class="btn btn-danger" data-key="delete" title="Hapus"><i class="fa-trash"></i></a>';
															}
														}
													} else {
														konten += '&nbsp;';
													}
													konten += '</td>';
												}
											}
										});
									});
									if ( f_show == ( f_show + res.jmlShow - 1 ) ) {
										var footer_info = 'Record ke ' + f_show  + ' dari ' + jmlLimit + ' record';
									} else {
										var footer_info = 'Record ke ' + f_show + ' - ' + ( f_show + res.jmlShow - 1 ) + ' dari ' + jmlLimit + ' record';
									}
									if( typeof res.jmlFilter != 'undefined' ) {
										footer_info += ' (difilter dari ' + res.jmlAll + ' record)';
									}

									$('.footer-serverside [data-info]').text(footer_info);
								}
							}
							e.find('tbody').html(konten);
							if($('[data-serverside][data-fixed]').length == 1) {
								$('[data-serverside]').parent().scrollTop(0);
							}
							if($('[data-serverside]').length == 1 && parseInt(res.jmlAll) > 0 && (refix == true || $('.fixed-table.header').length == 0)) {
								fixedTable('force');
							} else {
								freeze_col();
							}
						} else {
							cAlert.open(res.message,res.status);
						}
						proccess = false;
						var act_count = 0;
						for (var c in item_act) {
							act_count = act_count + 1;
						}
						if(act_count > 0) {
							$(document).on('contextmenu','.table-app tbody tr', function(e) {
								e.preventDefault();
								$('.context-action-body .context-menu-item').each(function(){
									if($(this).text() != 'Detail' && $(this).text() != 'Edit' && $(this).text() != 'Hapus' && $(this).text() != 'Aktiv' && $(this).text() != 'Non Aktiv') {
										$(this).addClass('hidden');
									}
								});
								var index = $(this).index() + 1;
								$('[data-fixed] tbody tr:nth-child('+index+')').find('td.button').children().each(function(){
									var btn_name = $(this).text();
									$('.context-menu-item').each(function(){
										if($(this).text() == btn_name) {
											$(this).removeClass('hidden');
										}
									});
								});
								setTimeout(function(){
									if($('.context-action-body .context-menu-item.hidden').length == $('.context-action-body .context-menu-item').length) {
										$(".table-app tbody tr").contextMenu("hide");
									}
								},100);
							});
							$.contextMenu({
						        selector: '.content-serverside .table-app tbody tr',
						        className: 'context-action-body',
						        callback: function(key, options) {
						        	var index = $(this).index() + 1;
						        	if($('[data-serverside] tbody tr:nth-child('+index+')').find('[data-key="'+key+'"]').length > 0) {
							        	if(typeof $(this).find('[data-key="'+key+'"]').attr('href') != 'undefined') {
							        		window.location = $(this).find('[data-key="'+key+'"]').attr('href');
							        	} else {
								        	$('[data-serverside] tbody tr:nth-child('+index+')').find('[data-key="'+key+'"]').trigger('click');
								        }
								    } else if(key == 'active') {
								    	var data_id = $('[data-fixed] tbody tr:nth-child('+index+')').find('.btn-input').attr('data-id');
								    	if(typeof active_inactive  === 'function') {
								    		active_inactive(data_id,'1');
								    	} else {
								    		cAlert.open('Fungsi Aktiv tidak tersedia');
								    	}
								    } else if(key == 'inactive') {
								    	var data_id = $('[data-fixed] tbody tr:nth-child('+index+')').find('.btn-input').attr('data-id');
								    	if(typeof active_inactive  === 'function') {
								    		active_inactive(data_id,'0');
								    	} else {
								    		cAlert.open('Fungsi Non Aktiv tidak tersedia');
								    	}
								    }
						        },
						        items: item_act
						    });
							$('.context-action-body').each(function(l,m){
								if(l > 0) {
									$(this).remove();
								}
							});
						}
						$('.footer-serverside select[data-page]').select2({
							width: 'resolve'
						});
						if(typeof $('[data-serverside]').attr('data-callback') != 'undefined') {
							var callback = $('[data-serverside]').attr('data-callback');
							var act = window[callback];
							if(typeof act == 'function') {
								act();
							} else {
								if(cek_kode == false) {
									cek_autocode();
								}	
							}
						} else {
							if(cek_kode == false) {
								cek_autocode();
							}
						}
					}
				});
			} else {
				proccess = false;
			}
		}
	}
}(jQuery));
function cek_autocode() {
	var cur_table = $('[data-table]').first().attr('data-table');
	$.ajax({
		url 	: base_url + 'settings/auto_code/check_table',
		data 	: {'table' : cur_table},
		type 	: 'post',
		dataType: 'json',
		success	: function(response){
			cek_kode = true;
			$.each(response,function(k,v){
				$('input[name="'+response[k]['kolom']+'"]').attr('placeholder','Auto generate saat save').attr('disabled',true).removeAttr('data-validation');
				$('input[name="validation_'+response[k]['kolom']+'"]').remove();
				$('input[name="field_'+response[k]['kolom']+'"]').remove();
			});
		}
	});
}
function fixedTable(e) {
	if($.browser.mozilla || $.browser.webkit) {
		if($('table[data-fixed="true"]').length > 0 && $('table[data-fixed="true"] tbody tr').length > 0) {
			if(has_fixed == false) {
				$('.fixed-content .content-body').scrollTop(0);
				$('.fixed-table.header').remove();
				var class_table	= $('[data-fixed="true"]').attr('class');
				var konten		= '<table class="'+class_table+'">';
				konten += '<thead>';
				konten += $('[data-fixed="true"] thead').html();
				konten += '</thead>';
				konten += '</table>';
				$('[data-fixed="true"]').parent().prepend('<div class="fixed-table header">'+konten+'</div>');
				var arr	= [];
				$('[data-fixed="true"] thead th').each(function(i,j){
					arr[i]	= parseFloat($(this).outerWidth());
					if(typeof $(this).attr('width') != 'undefined') {
						var w1 = $(this).attr('width');
						var w2 = w1.replace('px','');
						if(arr[i] < parseFloat(w2)) arr[i] = parseFloat(w2);
					}
					$(this).css({'min-width':arr[i],'max-width':arr[i]})
				});
				$('.fixed-table.header table thead th').each(function(i,j){
					$(this).css({'min-width':arr[i],'max-width':arr[i]})
				});
				has_fixed = true;
				$('.fixed-table.header table').css({'margin-left':left_fixed});
				$('[data-fixed] thead tr:nth-child(2) :input').each(function(){
					var index = $(this).parent().index() + 1;
					if($(this).attr('type') == 'checkbox') {
						if($(this).is(':checked')) {
							$('.fixed-table.header table thead tr:nth-child(2) th:nth-child('+index+')').children(':input').prop('checked',true);
						} else if($(this).is(':indeterminate')) {
							$('.fixed-table.header table thead tr:nth-child(2) th:nth-child('+index+')').children(':input').prop('indeterminate',true);
						}
					} else {
						var val = $(this).val();
						$('.fixed-table.header table thead tr:nth-child(2) th:nth-child('+index+')').children(':input').val(val);
					}
				});
				$.contextMenu({
			        selector: '.fixed-table.header table thead th', 
			        className: 'context-action-header',
			        callback: function(key, options) {
			        	var nth = $(this).index() + 1;
			        	$('[data-fixed] th:nth-child('+nth+')').attr('data-freeze',true);
			        	refixed = true;
			        	freeze_col();
			        },
			        items: {
			        	"freeze": {name: "Freeze", icon: "snowflake"}
			        }
			    });
			    $('.context-action-header').each(function(l,m){
					if(l > 0) {
						$(this).remove();
					}
				});
				if($('.fixed-table.header .select-boolean').length > 0) {
					$('.fixed-table.header .select-boolean').select2({
						minimumResultsForSearch: Infinity,
						width: 'resolve'
					});
				}
				if($('.fixed-table.header .select-replace').length > 0) {
					$('.fixed-table.header .select-replace').select2({
						minimumResultsForSearch: Infinity,
						width: 'resolve'
					});
				}
				if($('.fixed-table.header .dp-table').length > 0) {
					$('.fixed-table.header .dp-table').daterangepicker({
						singleDatePicker: true,
						showDropdowns: true,
						minYear: 1950,
						maxYear: parseInt(moment().format('YYYY'),10) + 3,
						locale: {
							format: 'DD/MM/YYYY'
						},
						autoUpdateInput: false
					}).on('apply.daterangepicker', function(ev, picker) {
						$(this).val(picker.startDate.format('DD/MM/YYYY'));
					}).mask('00/00/0000', {
						placeholder: "dd/mm/yyyy"
					}).change(function(e){
						var $t = $(this);
						var field = $t.attr('data-filter');
						var text = $t.val();
						if(text.length == 10) {
							$('[data-serverside] input[data-filter="'+field+'"]').val(text).trigger('change');
						} else {
							$('[data-serverside] input[data-filter="'+field+'"]').val('').trigger('change');
							$t.val('');
						}
					});
				}
				if($('.fixed-table.header .drp-table').length > 0) {
					$('.fixed-table.header .drp-table').daterangepicker({
						opens: 'left',
						showDropdowns: true,
						minYear: 1950,
						maxYear: parseInt(moment().format('YYYY'),10) + 3,
						locale: {
							format: 'DD/MM/YYYY',
							cancelLabel: 'Clear'
						},
						autoUpdateInput: false
					}).on('apply.daterangepicker', function(ev, picker) {
						var $t = $(this);
						var text = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY');
						if($t.val() != text){
							var field = $t.attr('data-filter');
							$t.val(text);
							$('[data-serverside] input[data-filter="'+field+'"]').val(text).trigger('change');
						}
					}).on('cancel.daterangepicker', function(ev, picker) {
						var $t = $(this);
						var field = $t.attr('data-filter');
						var text = '';
						$t.val(text);
						$('[data-serverside] input[data-filter="'+field+'"]').val(text);
						$('[data-serverside] input[data-filter="'+field+'"]').trigger('change');
					}).mask('00/00/0000 - 00/00/0000', {
						placeholder: "dd/mm/yyyy - dd/mm/yyyy"
					}).change(function(e){				
						var $t = $(this);
						var field = $t.attr('data-filter');
						var text = $t.val();
						if(text.length == 23) {
							$('[data-serverside] input[data-filter="'+field+'"]').val(text).trigger('change');
						} else {
							$('[data-serverside] input[data-filter="'+field+'"]').val('').trigger('change');
							$t.val('');
						}
					});
				}
				if($('[data-fixed] thead [data-freeze]').length > 0) {
					freeze_count = 0;
					freeze_col();
				}
			}
		}
		if(e == 'force') {
			has_fixed = false;
			syncHeaderTable();
		}
	}
}
function freeze_col(xforce) {
	if($('[data-fixed] tbody tr').length > 0  && typeof $('[data-fixed] tbody tr:nth-child(1) td:nth-child(1)').attr('colspan') == 'undefined') {
		if($('[data-fixed] tr:nth-child(1) [data-freeze]').length != freeze_count || typeof xforce != 'undefined') {
			if($('.fixed-table.header2').length == 0) {
				$('[data-fixed="true"]').parent().prepend('<div class="fixed-table header2"></div><div class="fixed-table body"></div>');
				$('.fixed-table.header2').append('<table class="'+$('.fixed-table.header table').attr('class')+'"><thead></thead></table>');
				$('.fixed-table.body').append('<table class="'+$('.fixed-table.header table').attr('class')+'"><tbody></tbody></table>');
			} else {
				$('.fixed-table.header2 table thead').html('');
				$('.fixed-table.body table thead').html('');
			}
			var konten = '';
			if($('[data-fixed] thead tr:nth-child(1) [data-freeze]').length > 0) {
				var q = 0;
				for(var i=1; i <= $('[data-fixed] thead tr').length; i++) {
					konten += '<tr>';
					$('.fixed-table.header thead tr:nth-child('+i+') [data-freeze]').each(function(){
						q++;
						konten += '<th';
						var h = $(this).outerHeight();
						var w = $(this).outerWidth();
						if($(this).index()+1 == $('.fixed-table.header thead tr:nth-child('+i+') th').length) w += 1;
						$.each($(this)[0].attributes, function() {
							if(this.specified) {
								if(this.name !== 'style') {
									konten += ' ' + this.name + '="' + this.value.replace('context-menu-active','') + '"';
								}
							}
						});
						konten += ' style="max-width:'+w+'px;width:'+w+'px;height:'+h+'px"';
						konten += ' data-index="'+q+'">'+$(this).html()+'</th>';
					});
					konten += '</tr>';
				}
				$('.fixed-table.header2 table thead').html(konten);
				if(refixed == true) {
					var t = {};
					var w = 1;
					$('[data-fixed] thead tr:nth-child(1) [data-freeze]').each(function(){
						t[w] = $(this).index() + 1;
						w++;
					});
					$('[data-fixed] thead tr:nth-child(1) th').each(function(){
						if(typeof $(this).attr('data-freeze') == 'undefined') {
							t[w] = $(this).index() + 1;
							w++;
						}
					});
					var konten_head_primary = '';
					var konten_body_primary = '';
					for(var i=1; i <= $('[data-fixed] thead tr').length; i++) {
						konten_head_primary += '<tr>';
						$.each(t,function(r,s){
							var $t_primary = $('[data-fixed] thead tr:nth-child('+i+') th:nth-child('+s+')');
							konten_head_primary += '<th';
							$.each($t_primary[0].attributes, function() {
								if(this.specified) {
									if(this.name !== 'style') {
										konten_head_primary += ' ' + this.name + '="' + this.value.replace('context-menu-active','') + '"';
									}
								}
							});
							konten_head_primary += '>'+$t_primary.html()+'</th>';
						});
						konten_head_primary += '</tr>';
					}
					for(var i=1; i <= $('[data-fixed] tbody tr').length; i++) {
						konten_body_primary += '<tr>';
						$.each(t,function(r,s){
							var $t_primary = $('[data-fixed] tbody tr:nth-child('+i+') td:nth-child('+s+')');
							konten_body_primary += '<td';
							$.each($t_primary[0].attributes, function() {
								if(this.specified) {
									if(this.name !== 'style') {
										konten_body_primary += ' ' + this.name + '="' + this.value.replace('context-menu-active','') + '"';
									}
								}
							});
							konten_body_primary += '>'+$t_primary.html()+'</td>';
						});
						konten_body_primary += '</tr>';
					}
					$('[data-fixed] thead').html(konten_head_primary);
					$('[data-fixed] tbody').html(konten_body_primary);
					$('.fixed-table').remove();
					refixed = false;
					has_fixed = false;
					fixedTable();
				} else {
					$('[data-fixed] thead tr:nth-child(2) :input').each(function(){
						var val = $(this).val();
						var index = $(this).parent().index() + 1;
						if($(this).hasClass('select-boolean')) {
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('span.select2').remove();
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('select').removeClass('select2-hidden-accessible').removeAttr('aria-hidden').removeClass('tabindex');
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('select').children().each(function(){
								$(this).removeAttr('data-select2-id');
							});
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('select').select2({
								minimumResultsForSearch: Infinity,
								width: 'resolve'
							});
						} else if($(this).hasClass('select-replace')) {
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('span.select2').remove();
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('select').removeClass('select2-hidden-accessible').removeAttr('aria-hidden').removeClass('tabindex');
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('select').children().each(function(){
								$(this).removeAttr('data-select2-id');
							});
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('select').select2({
								minimumResultsForSearch: Infinity,
								width: 'resolve'
							});
						} else if($(this).hasClass('dp-table')) {
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('input').daterangepicker({
								singleDatePicker: true,
								showDropdowns: true,
								minYear: 1950,
								maxYear: parseInt(moment().format('YYYY'),10) + 3,
								locale: {
									format: 'DD/MM/YYYY'
								},
								autoUpdateInput: false
							}).on('apply.daterangepicker', function(ev, picker) {
								$(this).val(picker.startDate.format('DD/MM/YYYY'));
								$(this).trigger('change');
							}).mask('00/00/0000', {
								placeholder: "dd/mm/yyyy"
							}).change(function(e){
								var $t = $(this);
								var field = $t.attr('data-filter');
								var text = $t.val();
								if(text.length == 10) {
									$('.fixed-table.header input[data-filter="'+field+'"]').val(text).trigger('change');
									$('[data-fixed] input[data-filter="'+field+'"]').val(text).trigger('change');
								} else {
									$('.fixed-table.header input[data-filter="'+field+'"]').val('').trigger('change');
									$('[data-fixed] input[data-filter="'+field+'"]').val('').trigger('change');
									$t.val('');
								}
							});
						} else if($(this).hasClass('drp-table')) {
							$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').find('input').daterangepicker({
								opens: 'left',
								showDropdowns: true,
								minYear: 1950,
								maxYear: parseInt(moment().format('YYYY'),10) + 3,
								locale: {
									format: 'DD/MM/YYYY',
									cancelLabel: 'Clear'
								},
								autoUpdateInput: false
							}).on('apply.daterangepicker', function(ev, picker) {
								var $t = $(this);
								var text = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY');
								if($t.val() != text){
									var field = $t.attr('data-filter');
									$t.val(text);
									$('.fixed-table.header input[data-filter="'+field+'"]').val(text).trigger('change');
								}
							}).on('cancel.daterangepicker', function(ev, picker) {
								var $t = $(this);
								var field = $t.attr('data-filter');
								var text = '';
								$t.val(text);
								$('.fixed-table.header input[data-filter="'+field+'"]').val(text);
								$('.fixed-table.header input[data-filter="'+field+'"]').trigger('change');
							}).mask('00/00/0000 - 00/00/0000', {
								placeholder: "dd/mm/yyyy - dd/mm/yyyy"
							}).change(function(e){				
								var $t = $(this);
								var field = $t.attr('data-filter');
								var text = $t.val();
								if(text.length == 23) {
									$('.fixed-table.header input[data-filter="'+field+'"]').val(text).trigger('change');
									$('[data-fixed] input[data-filter="'+field+'"]').val(text).trigger('change');
								} else {
									$('.fixed-table.header input[data-filter="'+field+'"]').val('').trigger('change');
									$('[data-fixed] input[data-filter="'+field+'"]').val('').trigger('change');
									$t.val('');
								}
							});
						}
						$('.fixed-table.header2 table thead tr:nth-child(2) [data-index="'+index+'"]').children().val(val).trigger('change');
					});
					$.contextMenu({
				        selector: '.fixed-table.header2 table thead th', 
				        className: 'context-action-header2',
				        callback: function(key, options) {
				        	if(key == 'unfreeze') {
					        	var nth = $(this).attr('data-index');
					        	$('[data-fixed] th:nth-child('+nth+')').removeAttr('data-freeze');
					        } else {
					        	$('[data-fixed] th[data-freeze]').removeAttr('data-freeze');
					        }
				        	refixed = true;
				        	freeze_col();
				        },
				        items: {
				        	"unfreeze": {name: "Unfreeze", icon: "sun"},
				        	"unfreeze_all": {name: "Unfreeze All", icon: "sun"},
				        }
				    });
				    $('.context-action-header2').each(function(l,m){
						if(l > 0) {
							$(this).remove();
						}
					});
					var body_top = $('.fixed-table.header2').offset().top + $('[data-fixed] thead').outerHeight();
					var body_bottom = 0;
					if($('.footer-serverside').length == 1) {
						body_bottom = $('.footer-serverside').outerHeight();
					}
					$('.fixed-table.body').css({'top':body_top+'px','bottom':body_bottom+'px'});
					if( $('.fixed-table.header table thead input[type="checkbox"]:checked').length == 1 ) {
						$('.fixed-table.header2 table thead input[type="checkbox"]').prop('checked',true);
					} else if( $('.fixed-table.header table thead input[type="checkbox"]:indeterminate').length == 1 ) {
						$('.fixed-table.header2 table thead input[type="checkbox"]').prop('indeterminate',true);
					}
					freeze_body();
				}
			} else {
				$('.fixed-table.header2,.fixed-table.body').remove();
			}
			freeze_count = $('[data-fixed] thead tr:nth-child(1) [data-freeze]').length;
		} else if($('.fixed-table.header2').length > 0) {
			freeze_body();
		}
	} else {
		$('.fixed-table.body table tbody').html('');
	}
}
function freeze_body() {
	konten = '';
	for(var i=1; i <= $('[data-fixed] tbody tr').length; i++) {
		konten += '<tr>';
		$('.fixed-table.header thead tr:nth-child(1) [data-freeze]').each(function(){
			var index = $(this).index() + 1;
			var $t = $('[data-fixed] tbody tr:nth-child('+i+') td:nth-child('+index+')');
			konten += '<td';
			var h = $t.outerHeight();
			var w = $(this).outerWidth();
			$.each($t[0].attributes, function() {
				if(this.specified) {
					if(this.name !== 'style') {
						konten += ' ' + this.name + '="' + this.value.replace('context-menu-active','') + '"';
					}
				}
			});
			konten += ' style="height:'+h+'px;width:'+w+'px;min-width:'+w+'px"';
			konten += '>'+$t.html()+'</td>';
		});
		konten += '</tr>';
	}
	$('.fixed-table.body table tbody').html(konten);
	$('[data-fixed] tbody input[type="checkbox"]:checked').each(function(){
		var index = $(this).closest('tr').index() + 1;
		$('.fixed-table.body table tbody tr:nth-child('+index+')').addClass('checked');
		$('.fixed-table.body table tbody tr:nth-child('+index+') input[type="checkbox"]').prop('checked',true);
	});
	$('.content-body').trigger('scroll');
}
function syncHeaderTable() {
	if($('.fixed-table.header').length > 0 && has_fixed == false) {
		$('table[data-fixed="true"]').scrollLeft(0);
		$('.fixed-table.header table').css({'margin-left':left_fixed});
		setTimeout(function(){
			var arr	= [];
			$('[data-fixed="true"] thead th').each(function(i,j){
				arr[i]	= parseFloat($(this).outerWidth());
				$(this).css({'min-width':arr[i],'max-width':arr[i]})
			});
			$('.fixed-table.header table thead th').each(function(i,j){
				$(this).css({'min-width':arr[i],'max-width':arr[i]})
			});
			$('.content-body').trigger('scroll');
			freeze_col();
		}, 200);
		has_fixed = true;
	}
}
$(function(){
	if($('[data-serverside]').length == 1) {
		serverside = $('[data-serverside]').serverside();
	}
	if($('table[data-fixed="true"]').length > 0) {
		fixedTable();
		setTimeout(function(){
			$('.content-body').trigger('scroll');
		},200);
	}
	$('#btn-minimize').click(function(){
		has_fixed = false;
		setTimeout(function(){
			syncHeaderTable();
		},100);
		freeze_col('force');
	});
	$(window).resize(function(){
		var cur_width = $(window).width();
		if(last_width != cur_width) {
			has_fixed = false;
			setTimeout(function(){
				syncHeaderTable();
			},100);
			freeze_col('force');
			last_width = cur_width;
		}
		if(typeof $('[data-serverside]').attr('data-fixed') == 'undefined') {
			setTimeout(function(){
				$('.footer-serverside').css({'width':$('[data-serverside]').outerWidth()});
			},500);
		}
	});
});
$('.content-body').scroll(function(){
	if($('.fixed-table.header').length > 0) {
		if($.browser.mozilla || $.browser.webkit) {
			var left 		= $(this).scrollLeft();
			var top 		= $(this).scrollTop();
			var freeze_left	= 0 - left;
			var freeze_top	= 0 - top;
			left_fixed 		= freeze_left;
			$('.fixed-table.header table').css({'margin-left':freeze_left});

			if($('.fixed-table.body').length > 0) {
				$('.fixed-table.body table').css({'margin-top':freeze_top});
			}
		}
	}
});
$(document).on('keyup','.fixed-table.header input[data-filter]',function(){
	if(!$(this).hasClass('dp-table') && !$(this).hasClass('drp-table')) {
		var field = $(this).attr('data-filter');
		var text = $(this).val();
		$('[data-serverside] input[data-filter="'+field+'"]').val(text).trigger('keyup');
	}
});
$(document).on('keyup','.fixed-table.header2 input[data-filter]',function(){
	if(!$(this).hasClass('dp-table') && !$(this).hasClass('drp-table')) {
		var field = $(this).attr('data-filter');
		var text = $(this).val();
		$('.fixed-table.header input[data-filter="'+field+'"]').val(text).trigger('keyup');
	}
});
$(document).on('change','.fixed-table.header select[data-filter]',function(){
	var field = $(this).attr('data-filter');
	var text = $(this).val();
	$('[data-serverside] select[data-filter="'+field+'"]').val(text).trigger('change');
});
$(document).on('change','.fixed-table.header2 select[data-filter]',function(){
	var field = $(this).attr('data-filter');
	var text = $(this).val();
	$('.fixed-table.header select[data-filter="'+field+'"]').val(text).trigger('change');
});
$(document).on('click','.fixed-table.header [data-sortable], .fixed-table.header2 [data-sortable]',function(){
	var cur_sort 	= $(this).children('[data-sort]').attr('data-sort');
	var next_sort	= '';
	if(cur_sort == 'both') next_sort = 'asc';
	else if(cur_sort == 'asc') next_sort = 'desc';
	else if(cur_sort == 'desc') next_sort = 'both';
	$(this).closest('.fixed-table').find('[data-sort]').attr('data-sort','both');
	$(this).children('[data-sort]').attr('data-sort',next_sort);

	var field = $(this).attr('data-field');
	if($(this).closest('.fixed-table').hasClass('header2')) {
		$('.fixed-table.header [data-sortable][data-field="'+field+'"]').trigger('click');
	} else {
		$('[data-serverside] [data-sortable][data-field="'+field+'"]').trigger('click');		
	}
});
$(document).on('click','.fixed-table.header thead th input[type="checkbox"]',function(){
	$('[data-serverside] thead th input[type="checkbox"]').trigger('click');
});
$(document).on('click','.fixed-table.header2 thead th input[type="checkbox"]',function(){
	$('.fixed-table.header thead th input[type="checkbox"]').trigger('click');
});
$(document).on('click','.fixed-table.body tbody td input[type="checkbox"]',function(){
	var index = $(this).closest('tr').index() + 1;
	$('[data-fixed] tbody tr:nth-child('+index+') input[type="checkbox"]').trigger('click');
});
$(document).on('mouseenter','.table-hover[data-fixed="true"] tbody tr',function(){
	var index = $(this).index() + 1;
	$('.fixed-table.body .table-hover tbody tr:nth-child('+index+')').addClass('hover');
});
$(document).on('mouseleave','.table-hover[data-fixed="true"] tbody tr',function(){
	var index = $(this).index() + 1;
	$('.fixed-table.body .table-hover tbody tr:nth-child('+index+')').removeClass('hover');
});
$(document).on('mouseenter','.fixed-table.body .table-hover tbody tr',function(){
	var index = $(this).index() + 1;
	$('.table-hover[data-fixed="true"] tbody tr:nth-child('+index+')').addClass('hover');
});
$(document).on('mouseleave','.fixed-table.body .table-hover tbody tr',function(){
	var index = $(this).index() + 1;
	$('.table-hover[data-fixed="true"] tbody tr:nth-child('+index+')').removeClass('hover');
});