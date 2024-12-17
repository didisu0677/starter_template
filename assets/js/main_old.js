var del_id 		= null;
var act_field	= 'is_active';
var act_id 		= null;
var act_val 	= null;
var saved_id 	= 0;
var response_edit = {};
var urlDelete 	= '';

function deleteData() {
	var delUrl = '';
	if(urlDelete != '') delUrl = urlDelete;
	else {
		if(typeof $('#modal-form form').attr('data-delete') != 'undefined') {
			delUrl = $('#modal-form form').attr('data-delete');
		} else {
			var curUrl = $('#modal-form form').attr('action');
			if(typeof curUrl == 'undefined') {
				var curUrl = $('[data-serverside]').attr('data-serverside');
			}
			var parseUrl = curUrl.split('/');
			var lastPath = parseUrl[parseUrl.length - 1];
			if(lastPath == '') lastPath = parseUrl[parseUrl.length - 2];
			delUrl = curUrl.replace(new RegExp(lastPath + '$'),'delete');
		}
	}
	var s_id = del_id.split(',');
	var post_id = s_id.length > 1 ? s_id : del_id;
	$.ajax({
		url			: delUrl,
		data 		: {'id':post_id},
		type		: 'post',
		cache		: false,
		dataType	: 'json',
		success 	: function(response) {
			if(response.status == 'success') {
				cAlert.open(response.message,response.status,'refreshData');
			} else {
				cAlert.open(response.message,response.status);
			}
		}
	});
}
function activeData() {
	var actUrl 	= $('#modal-form form').attr('action');
	var post_id = act_id.split(',');
	var fld 	= typeof(act_field) == 'undefined' ? 'is_active' : act_field;
	var datapost= {};
	datapost['id']	= post_id;
	datapost[fld]	= act_val;
	$.ajax({
		url			: actUrl,
		data 		: datapost,
		type		: 'post',
		cache		: false,
		dataType	: 'json',
		success 	: function(response) {
			if(response.status == 'success') {
				cAlert.open(response.message,response.status,'refreshData');
			} else {
				cAlert.open(response.message,response.status);
			}
		}
	});
}
function reload() {
	location.reload();
}
function recursive_change(name) {
	var spl_child = name.split('|');
	$.each(spl_child,function(k,v){
		if($('[name="'+v+'"]').length > 0) {
			$('[name="'+v+'"]').trigger('change');
			if(typeof $('[name="'+v+'"]').attr('data-child') != 'undefined') {
				recursive_change($('[name="'+v+'"]').attr('data-child'));
			}
		}
	});
}
function active_inactive(a_id,a_val,field) {
	if(typeof field == 'undefined') field = 'is_active';
	act_id 		= a_id;
	act_val 	= a_val;
	act_field	= field;
	if(a_val == '1' && $('.btn-input[data-id="'+a_id+'"]').closest('tr').find('.badge-success').length == 1 && field == 'is_active') {
		cAlert.open(lang.data_ini_sudah_aktif);
	} else if(a_val == '0' && $('.btn-input[data-id="'+a_id+'"]').closest('tr').find('.badge-danger').length == 1 && field == 'is_active') {
		cAlert.open(lang.data_ini_sudah_tidak_aktif);
	} else {
		var kata = '';
		if(field == 'is_active') {
			kata = act_val == '1' ? lang.aktifkan_data_ini : lang.tidak_aktifkan_data_ini;
		} else {
			kata = lang.ubah_data_ini;
		}
		cConfirm.open(kata + '?','activeData');
	}
}
function listDataPage() {
	if($('form[data-page]').length == 1) {
		window.location = $('form[data-page]').attr('data-page');
	} else {
		reload();
	}
}
function datepicker_init() {
	$('.dp').each(function(){
		var placeholder = '';
		if(typeof $(this).attr('placeholder') != 'undefined') placeholder = $(this).attr('placeholder');
		if(placeholder == '') placeholder = "dd/mm/yyyy";
		$(this).mask('00/00/0000', {placeholder: placeholder});
	});
	$('.dp:not([readonly])').each(function(){
		var mindate = false;
		var maxdate = false;
		if(typeof $(this).attr('data-mindate') != 'undefined') {
			mindate = new Date($(this).attr('data-mindate'));
		}
		if(typeof $(this).attr('data-maxdate') != 'undefined') {
			maxdate = new Date($(this).attr('data-maxdate'));
		}
		$(this).daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			minDate: mindate,
			maxDate: maxdate,
			locale: {
				format: 'DD/MM/YYYY',
				cancelLabel: lang.batal,
				applyLabel: lang.ok,
				daysOfWeek: [lang.min, lang.sen, lang.sel, lang.rab, lang.kam, lang.jum, lang.sab],
				monthNames: [lang.jan, lang.feb, lang.mar, lang.apr, lang.mei, lang.jun, lang.jul, lang.agu, lang.sep, lang.okt, lang.nov, lang.des]
			},
			autoUpdateInput: false
		}, function(start, end, label) {
			$(this.element[0]).removeClass('is-invalid');
			$(this.element[0]).parent().find('.error').remove();
		}).on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD/MM/YYYY'));
			var act = window[$(this).attr('id') + '_callback'];
			if(typeof act == 'function') {
				act();
			}
		}).on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			var act = window[$(this).attr('id') + '_callback'];
			if(typeof act == 'function') {
				act();
			}
		});
	});
}

function daterangepicker_init() {
	$('.drp').each(function(){
		var placeholder = '';
		if(typeof $(this).attr('placeholder') != 'undefined') placeholder = $(this).attr('placeholder');
		if(placeholder == '') placeholder = "dd/mm/yyyy - dd/mm/yyyy";
		$(this).mask('00/00/0000 - 00/00/0000', {placeholder: placeholder});
	});
	$('.drp:not([readonly])').each(function(){
		var mindate = false;
		var maxdate = false;
		if(typeof $(this).attr('data-mindate') != 'undefined') {
			mindate = new Date($(this).attr('data-mindate'));
		}
		if(typeof $(this).attr('data-maxdate') != 'undefined') {
			maxdate = new Date($(this).attr('data-maxdate'));
		}
		$(this).daterangepicker({
			showDropdowns: true,
			minDate: mindate,
			maxDate: maxdate,
			locale: {
				format: 'DD/MM/YYYY - DD/MM/YYYY',
				cancelLabel: lang.batal,
				applyLabel: lang.ok,
				daysOfWeek: [lang.min, lang.sen, lang.sel, lang.rab, lang.kam, lang.jum, lang.sab],
				monthNames: [lang.jan, lang.feb, lang.mar, lang.apr, lang.mei, lang.jun, lang.jul, lang.agu, lang.sep, lang.okt, lang.nov, lang.des]
			},
			autoUpdateInput: false
		}, function(start, end, label) {
			$(this.element[0]).removeClass('is-invalid');
			$(this.element[0]).parent().find('.error').remove();
		}).on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
			var act = window[$(this).attr('id') + '_callback'];
			if(typeof act == 'function') {
				act();
			}
		}).on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			var act = window[$(this).attr('id') + '_callback'];
			if(typeof act == 'function') {
				act();
			}
		});
	});
}
function datetimepicker_init() {
	$('.dtp').each(function(){
		var placeholder = '';
		if(typeof $(this).attr('placeholder') != 'undefined') placeholder = $(this).attr('placeholder');
		if(placeholder == '') placeholder = "dd/mm/yyyy hh:ii";
		$(this).mask('00/00/0000 00:00', {placeholder: placeholder});
	});
	$('.dtp:not([readonly])').each(function(){
		var mindate = false;
		var maxdate = false;
		if(typeof $(this).attr('data-mindate') != 'undefined') {
			mindate = new Date($(this).attr('data-mindate'));
		}
		if(typeof $(this).attr('data-maxdate') != 'undefined') {
			maxdate = new Date($(this).attr('data-maxdate'));
		}
		$(this).daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			timePicker: true,
			timePicker24Hour: true,
			minDate: mindate,
			maxDate: maxdate,
			locale: {
				format: 'DD/MM/YYYY HH:mm',
				cancelLabel: lang.batal,
				applyLabel: lang.ok,
				daysOfWeek: [lang.min, lang.sen, lang.sel, lang.rab, lang.kam, lang.jum, lang.sab],
				monthNames: [lang.jan, lang.feb, lang.mar, lang.apr, lang.mei, lang.jun, lang.jul, lang.agu, lang.sep, lang.okt, lang.nov, lang.des]
			},
			autoUpdateInput: false
		}, function(start, end, label) {
			$(this.element[0]).removeClass('is-invalid');
			$(this.element[0]).parent().find('.error').remove();
		}).on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD/MM/YYYY HH:mm'));
			var act = window[$(this).attr('id') + '_callback'];
			if(typeof act == 'function') {
				act();
			}
		}).on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			var act = window[$(this).attr('id') + '_callback'];
			if(typeof act == 'function') {
				act();
			}
		});
	});
}
$(function(){
	if (!/Android|webOS|iPhone|iPad|BlackBerry|Windows Phone|Opera Mini|IEMobile|Mobile/i.test(navigator.userAgent)) {
		const ps = new PerfectScrollbar('#sidebar-panel');
		$('#sidebar-panel').mouseenter(function(){
			ps.update();
		});

		if($('table[data-fixed]').length > 0) {
			const ps_body = new PerfectScrollbar('.content-body');
			$('.content-body').mouseenter(function(){
				ps_body.update();
			});
		}
	} else {
		$('#sidebar-panel').css({'overflow-y':'auto'});
	}

	$(".money:not([readonly])").maskMoney({allowNegative: true, thousands:'.', decimal:',', precision: 0});
	$(".tags").tagsinput();
	$(".percent:not([readonly])").each(function(){
		var placeholder = '';
		if(typeof $(this).attr('placeholder') != 'undefined') placeholder = $(this).attr('placeholder');
		$(this).mask('099,09',{placeholder : placeholder});
	});

	datepicker_init();
	datetimepicker_init();
	daterangepicker_init();

	$('.icp').iconpicker();
	$('.icp').on('iconpickerSelected', function (e) {
		$(this).removeClass('is-invalid');
		$(this).closest('.input-group').parent().find('.error').remove();
	});

	$('.icp').keypress(function(e){
		if(e.which == 0) return true;
		return false;
	});

	$('.select2').each(function(){
		var $t = $(this);
		var placeholder = typeof $t.attr('data-placeholder') == 'undefined' ? '' : $t.attr('data-placeholder');
		if($t.closest('.modal').length > 0) {
			if($t.hasClass('infinity')) {
				if(typeof $t.attr('data-width') != 'undefined') {
					$t.select2({
						placeholder: placeholder,
						minimumResultsForSearch: Infinity,
						dropdownParent : $t.parent(),
						width: $t.attr('data-width')
					});
				} else {
					$t.select2({
						placeholder: placeholder,
						minimumResultsForSearch: Infinity,
						dropdownParent : $t.parent()
					});
				}
			} else {
				$t.select2({
					placeholder: placeholder,
					dropdownParent : $t.parent()
				});
			}
		} else {
			if($t.hasClass('infinity')) {
				if(typeof $t.attr('data-width') != 'undefined') {
					$t.select2({
						placeholder: placeholder,
						minimumResultsForSearch: Infinity,
						width: $t.attr('data-width')
					});
				} else {
					$t.select2({
						placeholder: placeholder,
						minimumResultsForSearch: Infinity
					});
				}
			} else {
				if(typeof $t.attr('data-width') != 'undefined') {
					$t.select2({
						placeholder: placeholder,
						width: $t.attr('data-width')
					});
				} else {
					$t.select2({
						placeholder: placeholder
					});
				}
			}
		}
	});

	$('.image-upload img').each(function(i,j){
		var idx 	= 'upl-img-' + i;
		var konten 	= '<form action="'+$(this).attr('data-action')+'" class="hidden">';
		konten += '<input type="file" name="image" class="input-image" accept="image/*" id="'+idx+'">';
		konten += '<input type="hidden" name="name" value="'+$(this).parent().children('input').attr('name')+'">';
		konten += '<input type="hidden" name="token" value="'+$(this).attr('data-token')+'">';
		konten += '</form>';
		$(this).attr('data-image',idx);
		$(this).parent().parent().children('.image-description').attr('data-origin',$(this).parent().parent().children('.image-description').text());
		$('body').append(konten);

		$('#' + idx).fileupload({
			maxFileSize: upl_flsz,
			autoUpload: false,
			dataType: 'text',
			acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
		}).on('fileuploadadd', function(e, data) {
			$('[data-image="'+idx+'"]').parent().parent().find('.image-description').addClass('waiting');
			data.process();
		}).on('fileuploadprocessalways', function (e, data) {
			if (data.files.error) {
				data.abort();
				cAlert.open(lang.file_yang_diizinkan + ' *.png, *.jpg, ' + lang.atau + ' *.gif ' + lang.dengan_ukuran_maksimal + ' ' + (upl_flsz / 1024 / 1024) + 'MB');
				var text = $('[data-image="'+idx+'"]').parent().parent().find('.image-description').attr('data-origin');
				$('[data-image="'+idx+'"]').parent().parent().find('.image-description').text(text).removeClass('waiting');
			} else {
				data.submit();
			}
		}).on('fileuploadprogressall', function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('[data-image="'+idx+'"]').parent().parent().find('.image-description').text('Progress : ' + progress + '%');
		}).on('fileuploaddone', function (e, data) {
			var text = $('[data-image="'+idx+'"]').parent().parent().find('.image-description').attr('data-origin');
			$('[data-image="'+idx+'"]').parent().parent().find('.image-description').text(text).removeClass('waiting');
			if(data.result == 'invalid') {
				cAlert.open(lang.file_gagal_diunggah,'error');
			} else {
				$('[data-image="'+idx+'"]').attr('src',base_url + data.result + '?' + new Date().getTime());
				$('[data-image="'+idx+'"]').parent().find('input').val(data.result);
			}
		}).on('fileuploadfail', function (e, data) {
			cAlert.open(lang.file_gagal_diunggah,'error');
			var text = $('[data-image="'+idx+'"]').parent().parent().find('.image-description').attr('data-origin');
			$('[data-image="'+idx+'"]').parent().parent().find('.image-description').text(text).removeClass('waiting');
		}).on('fileuploadalways', function() {
		});
	});

	$('.input-image').each(function(i,j){
		if(typeof $(this).attr('data-action') != undefined) {
			var idx 	= 'upl-img2-' + i;
			var konten 	= '<form action="'+$(this).attr('data-action')+'" class="hidden">';
			konten += '<input type="file" name="image" class="input-image" accept="image/*" id="'+idx+'">';
			konten += '<input type="hidden" name="name" value="'+$(this).parent().children('input').attr('name')+'">';
			konten += '<input type="hidden" name="token" value="'+$(this).attr('data-token')+'">';
			konten += '</form>';
			$(this).attr('data-image',idx);
			$(this).parent().find('button.btn-image').attr('data-image',idx);
			$('body').append(konten);

			$('#' + idx).fileupload({
				maxFileSize: upl_flsz,
				autoUpload: false,
				dataType: 'text',
				acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
			}).on('fileuploadadd', function(e, data) {
				$('button[data-image="'+idx+'"]').attr('disabled',true);
				data.process();
			}).on('fileuploadprocessalways', function (e, data) {
				if (data.files.error) {
					data.abort();
					cAlert.open(lang.file_yang_diizinkan + ' *.png, *.jpg, ' + lang.atau + ' *.gif ' + lang.dengan_ukuran_maksimal + ' ' + (upl_flsz / 1024 / 1024) + 'MB');
					$('button[data-image="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
				} else {
					data.submit();
				}
			}).on('fileuploadprogressall', function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('button[data-image="'+idx+'"]').text(progress + '%');
			}).on('fileuploaddone', function (e, data) {
				if(data.result == 'invalid') {
					cAlert.open(lang.file_gagal_diunggah,'error');
				} else {
					$('input[data-image="'+idx+'"]').val(data.result);
				}
				$('button[data-image="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			}).on('fileuploadfail', function (e, data) {
				cAlert.open(lang.file_gagal_diunggah,'error');
				$('button[data-image="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			}).on('fileuploadalways', function() {
			});
		}
	});

	$('.btn-view-imageupload').popover({
		html: true,
		trigger: 'click',
		placement: 'left',
		title: function(){
			return $(this).closest('.form-group').children('label').text();
		},
		content: function () {
			var fl = $(this).closest('.input-group').children(':input').val();
			if(fl) return '<img src="'+base_url+fl+ '?' + new Date().getTime()+'" width="250px" />';
			else return lang.tidak_ada_gambar;
		}
	});


	$('.input-file').each(function(i,j){
		var idx 	= 'upl-file-' + i;
		var konten 	= '<form action="'+$(this).attr('data-action')+'" class="hidden">';
		var accept 	= typeof $(this).attr('data-accept') == 'undefined' ? Base64.decode(upl_alw) : $(this).attr('data-accept');
		var regex 	= "(\.|\/)("+accept+")$";
		var re 		= accept == '*' ? '*' : new RegExp(regex,"i");
		var name 	= $(this).parent().children('input').attr('name');
		var nm_attr	= name.replace('[','_').replace(']','');
		konten += '<input type="file" name="document" class="input-file" id="'+idx+'">';
		konten += '<input type="hidden" name="name" value="'+nm_attr+'">';
		konten += '<input type="hidden" name="token" value="'+$(this).attr('data-token')+'">';
		konten += '</form>';
		$(this).attr('data-file',idx);
		$(this).parent().find('.fileupload-preview').attr('data-file',idx);
		$(this).parent().find('button').attr('data-file',idx);
		$('body').append(konten);

		if(re == '*') {
			$('#' + idx).fileupload({
				maxFileSize: upl_flsz,
				autoUpload: false,
				dataType: 'text',
			}).on('fileuploadadd', function(e, data) {
				$('button[data-file="'+idx+'"]').attr('disabled',true);
				data.process();
			}).on('fileuploadprocessalways', function (e, data) {
				if (data.files.error) {
					cAlert.open('Tidak dapat mengupload file ini. ' + lang.ukuran_file_maks + ' : ' + (upl_flsz / 1024 / 1024) + 'MB');
					$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
				} else {
					data.submit();
				}
			}).on('fileuploadprogressall', function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('button[data-file="'+idx+'"]').text(progress + '%');
			}).on('fileuploaddone', function (e, data) {
				if(data.result == 'invalid' || data.result == '') {
					cAlert.open(lang.file_gagal_diunggah,'error');
				} else {
					var spl_result = data.result.split('/');
					if(spl_result.length == 1) spl_result = data.result.split('\\');
					if(spl_result.length > 1) {
						var spl_last_str = spl_result[spl_result.length - 1].split('.');
						if(spl_last_str.length == 2) {
							$('.fileupload-preview[data-file="'+idx+'"]').html('<a href="'+base_url+data.result+'" target="_blank">'+spl_result[spl_result.length - 1]+'</a>');
							$('input[data-file="'+idx+'"]').val(data.result);
						} else {
							cAlert.open(lang.file_gagal_diunggah,'error');
						}
					} else {
						cAlert.open(lang.file_gagal_diunggah,'error');						
					}
				}
				$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			}).on('fileuploadfail', function (e, data) {
				cAlert.open('File gagal diupload','error');
				$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			}).on('fileuploadalways', function() {
			});
		} else {
			$('#' + idx).fileupload({
				maxFileSize: upl_flsz,
				autoUpload: false,
				dataType: 'text',
				acceptFileTypes: re
			}).on('fileuploadadd', function(e, data) {
				$('button[data-file="'+idx+'"]').attr('disabled',true);
				data.process();
			}).on('fileuploadprocessalways', function (e, data) {
				if (data.files.error) {
					data.abort();
					var explode = accept.split('|');
					var acc 	= '';
					$.each(explode,function(i){
						if(i == 0) {
							acc += '*.' + explode[i];
						} else if (i == explode.length - 1) {
							acc += ', ' + lang.atau + ' *.' + explode[i];
						} else {
							acc += ', *.' + explode[i];
						}
					});
					cAlert.open(lang.file_yang_diizinkan + ' ' + acc + '. ' + lang.ukuran_file_maks + ' : ' + (upl_flsz / 1024 / 1024) + 'MB');
					$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
				} else {
					data.submit();
				}
			}).on('fileuploadprogressall', function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('button[data-file="'+idx+'"]').text(progress + '%');
			}).on('fileuploaddone', function (e, data) {
				if(data.result == 'invalid' || data.result == '') {
					cAlert.open(lang.file_gagal_diunggah,'error');
				} else {
					var spl_result = data.result.split('/');
					if(spl_result.length == 1) spl_result = data.result.split('\\');
					if(spl_result.length > 1) {
						var spl_last_str = spl_result[spl_result.length - 1].split('.');
						if(spl_last_str.length == 2) {
							$('.fileupload-preview[data-file="'+idx+'"]').html('<a href="'+base_url+data.result+'" target="_blank">'+spl_result[spl_result.length - 1]+'</a>');
							$('input[data-file="'+idx+'"]').val(data.result);
						} else {
							cAlert.open(lang.file_gagal_diunggah,'error');
						}
					} else {
						cAlert.open(lang.file_gagal_diunggah,'error');						
					}
				}
				$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			}).on('fileuploadfail', function (e, data) {
				cAlert.open(lang.file_gagal_diunggah,'error');
				$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			}).on('fileuploadalways', function() {
			});
		}
	});

	$('.image-upload img').click(function(){
		if(!$(this).parent().parent().children('.image-description').hasClass('waiting')) {
			$('#' + $(this).attr('data-image')).click();
		}
	});

	if(typeof $('body').attr('data-status-open') != 'undefined') {
		var t = lang.informasi;
		if($('body').attr('data-status-open') == 'error') {
			t = lang.kesalahan;
		}else if($('body').attr('data-status-open') == 'warning') {
			t = lang.peringatan;
		}else if($('body').attr('data-status-open') == 'success') {
			t = lang.berhasil;
		}
		$.toast({
			heading: t,
			text: $('body').attr('data-message-open'),
			showHideTransition: 'plain',
			icon: $('body').attr('data-status-open'),
			position: 'bottom-right',
			loader: false
		});
	}

});
$(document).on('click','[data-file]',function(){
	$('#' + $(this).attr('data-file')).trigger('click');
});
$(document).on('click','[data-image]',function(){
	if($(this)[0].nodeName == 'BUTTON') {
		$('#' + $(this).attr('data-image')).trigger('click');
	}
});
$('[data-submit="ajax"]').submit(function(e) {
	e.preventDefault();
	var f = $(this);
	var proc = true;
	if(typeof f.attr('data-trigger') !== 'undefined') {
		if(f.attr('data-trigger') != "") {
			var act = window[f.attr('data-trigger')];
			if(typeof act == 'function') {
				proc = act();
			}
		}
	}
	if(validation($(this).attr('id')) && proc) {
		$.ajax({
			url		: $(this).attr('action'),
			data 	: $(this).serialize(),
			type	: 'post',
			dataType: 'json',
			success	: function(response) {
				if(response.status == 'success') {
					var x  = f.attr('data-callback');
					if(typeof x != 'undefined' && x && typeof window[x] === 'function') {
						cAlert.open(response.message,response.status,f.attr('data-callback'));
					} else {
						cAlert.open(response.message,response.status);
						f[0].reset();
					}
				} else {
					cAlert.open(response.message,response.status);
				}
			}
		});
	}
});
$('.modal form').submit(function(e){
	e.preventDefault();
	if(typeof $(this).attr('data-manual') == 'undefined') {
		var callback = 'refreshData';
		if(typeof $(this).attr('data-callback') != 'undefined') {
			callback = $(this).attr('data-callback');
		}
		var proc = true;
		if(typeof $(this).attr('data-trigger') !== 'undefined') {
			if($(this).attr('data-trigger') != "") {
				var act = window[$(this).attr('data-trigger')];
				if(typeof act == 'function') {
					proc = act();
				}
			}
		}
		if(validation($(this).attr('id')) && proc) {
			$.ajax({
				url 	: $(this).attr('action'),
				data 	: $(this).serialize(),
				type 	: 'post',
				dataType: 'json',
				success : function(response) {
					if(response.status == 'success') {
						saved_id = response.id;
						cAlert.open(response.message,response.status,callback);
					} else {
						cAlert.open(response.message,response.status);
					}
				}
			});
		}
	}
});
$(document).on('click','.btn-input',function(e){
	e.preventDefault();
	if(typeof $('#modal-form').attr('data-manual') == 'undefined') {
		proccess = false;
		$('select[data-child]').each(function(){
			var data_child = $(this).attr('data-child');
			var spl_child = data_child.split('|');
			$.each(spl_child,function(k,v){
				$('select[name="'+v+'"]').html('').trigger('change');
			});
		});
		$('select[multiple] option').prop('selected',false);
		var mtitle = $('#modal-form .modal-title').text();
		var autoTitle = true;
		if($('#modal-form .modal-title').text() != '' && $('#modal-form .modal-title').text() != 'Modal' && $('#modal-form .modal-title').text() != lang.tambah && $('#modal-form .modal-title').text() != lang.ubah) {
			autoTitle = false;
		}
		$('#modal-form input[type=hidden]').each(function(){
			if($(this).attr('name').indexOf("field") == -1 && $(this).attr('name').indexOf("validation") == -1 && $(this).attr('id') == 'undefined') {
				$(this).val('');
			}
		});
		if($('#modal-form .wizard').length > 0) {
			$('#modal-form .modal-body.wizard a').removeClass('active').attr('aria-selected','false');
			$('#modal-form .modal-body.wizard li:first-child a').addClass('active').attr('aria-selected','true');
			$('#modal-form .wizard .tab-content .tab-pane').removeClass('show').removeClass('active');
			$('#modal-form .wizard .tab-content .tab-pane:first-child').addClass('show').addClass('active');
		}
		$('#modal-form form')[0].reset();
		$('#modal-form input[type=radio]').each(function(){
			if($(this).is(':checked')) {
				$(this).trigger('click');
			}
		});
		$('#modal-form .modal-footer').html('').addClass('hidden');
		$('[data-edit-readonly]').removeAttr('readonly');
		if($(this).data('id') == 0) {
			if($('#password').length > 0) {
				$('#password').attr('data-validation','required|min-length:6').removeAttr('placeholder');
			}
			if(autoTitle) {
				mtitle = typeof $(this).attr('aria-label') != 'undefined' ? $(this).attr('aria-label') : lang.tambah;
				$('#modal-form [type="submit"]').text(lang.simpan);
			}
		} else {
			if(autoTitle) {
				mtitle = lang.ubah;
				$('#modal-form [type="submit"]').text(lang.perbaharui);
			}
		}
		$('#modal-form form .is-invalid').each(function(){
			$(this).removeClass('is-invalid');
			$(this).closest('.form-group').find('.error').remove();
		});
		var openCallback  = $('#modal-form').attr('data-openCallback');
		response_edit = {};
		if($('#modal-form [data-editor]').length > 0 && typeof window.CKEDITOR != 'undefined') {
			$('#modal-form [data-editor]').each(function(){
				CKEDITOR.instances[$(this).attr('id')].setData('');
			});
		}
		if($(this).attr('data-id') == '0') {
			if($('#modal-form .wizard').length > 0) {
				$('#modal-form .modal-body.wizard .nav-tabs li a').removeAttr('data-toggle');
				$('#modal-form .modal-body.wizard .nav-tabs li:first-child a').attr('data-toggle','tab');
			}
			$('#modal-form .modal-title').html(mtitle);
			$('#modal-form').modal();
			$('#modal-form [name="id"]').val(0);
			if($('#modal-form select').length > 0) {
				$('#modal-form select').trigger('change');
			}
			if ($('#modal-form  form .icp').length > 0) {
				$('#modal-form  form .icp').each(function(){
					$(this).closest('.input-group').find('.input-group-text').html('');
				});
			}
			$('[data-validation="image"]').each(function(){
				$(this).parent().find('img').attr('src', $(this).parent().find('img').attr('data-origin') );
				$(this).val('');
			});
			proccess = true;
			if(typeof openCallback != 'undefined' && openCallback && typeof window[openCallback] === 'function') {
				var act = window[openCallback];
				act();
			}
		} else {
			var getUrl = '';
			if(typeof $('#modal-form form').attr('data-edit') != 'undefined') {
				getUrl = $('#modal-form form').attr('data-edit');
			} else {
				var curUrl = $('#modal-form form').attr('action');
				var parseUrl = curUrl.split('/');
				var lastPath = parseUrl[parseUrl.length - 1];
				if(lastPath == '') lastPath = parseUrl[parseUrl.length - 2];
				getUrl = curUrl.replace(lastPath,'get_data');
			}
			$.ajax({
				url			: getUrl,
				data 		: {'id':$(this).attr('data-id')},
				type		: 'post',
				cache		: false,
				dataType	: 'json',
				success		: function(response) {
					if(typeof response['status'] == 'undefined' || typeof response['message'] == 'undefined') {
						response_edit = response;
						if($('#modal-form .wizard').length > 0) {
							$('#modal-form .modal-body.wizard .nav-tabs li a').attr('data-toggle','tab');
						}
						$('#modal-form form :input').each(function(){
							var res_value = response[$(this).attr('name')] == null ? '' : response[$(this).attr('name')];
							if(typeof $(this).attr('name') != 'undefined' && ($(this).attr('name').indexOf("field") == 0 || $(this).attr('name').indexOf("validation") == 0) && res_value == '') {
								res_value = $(this).val();
							}
							if( $(this).attr('type') == 'checkbox') {
								if(res_value == $(this).attr('value'))
									$(this).prop('checked',true);
								else
									$(this).prop('checked',false);
							} else if($(this).attr('type') == 'radio') {
								if(res_value == $(this).attr('value'))
									$(this).prop('checked',true).trigger('click');
								else
									$(this).prop('checked',false);
							} else if($(this).attr('type') == 'hidden' && typeof $(this).attr('data-validation') != 'undefined' && $(this).attr('data-validation') == 'image') {
								$(this).val(res_value);
								if(typeof response['dir_upload'] != 'undefined' && res_value != '') {
									$(this).parent().children('img').attr('src', response['dir_upload'] + res_value);
								}
							} else if(typeof $(this).attr('data-editor') != 'undefined' && typeof window.CKEDITOR != 'undefined') {
								$(this).val(res_value);
								CKEDITOR.instances[$(this).attr('id')].setData(decodeEntities(res_value));
							} else {
								if(typeof res_value != 'undefined') {
									var c_date = res_value.split('-');
									var c_datetime = res_value.split(' ');
									var is_datetime = false;
									if(res_value.length == 19 && c_datetime.length == 2) {
										var dt_date = c_datetime[0].split('-');
										var dt_time = c_datetime[1].split(':');
										if(dt_date.length == 3 && dt_time.length == 3) {
											if(res_value == '0000-00-00 00:00:00') {
												$(this).val('');
											} else {
												$(this).val(dt_date[2]+'/'+dt_date[1]+'/'+dt_date[0]+' '+dt_time[0]+':'+dt_time[1]).trigger('change');
											}
											is_datetime = true;
										}
									}
									if(!is_datetime) {
										if(c_date.length == 3 && c_date[0].length == 4 && c_date[1].length == 2 && c_date[2].length == 2) {
											if(res_value == '0000-00-00') {
												$(this).val('');
											} else {
												$(this).val(c_date[2]+'/'+c_date[1]+'/'+c_date[0]);
											}
										} else {
											if(typeof response['opt_' + $(this).attr('name')] != 'undefined') {
												$(this).html(response['opt_' + $(this).attr('name')]).val(res_value);
												if($(this).val() == null && $(this).is('select')) {
													var vl_dt = res_value;
													var el_vl = $(this);
													$(this).children().each(function(){
														var vl = $(this).attr('value').toUpperCase();
														if(vl == vl_dt) {
															el_vl.val($(this).attr('value'));
														}
													});
												}
											}  else {
												if($(this).hasClass('money')) {
													$(this).val(numberFormat(res_value,0,',','.','negatif'));
												} else if($(this).hasClass('percent')) {
													$(this).val(cPercent(res_value));
												} else {
													$(this).val(res_value);
													if($(this).val() == null && $(this).is('select')) {
														var vl_dt = res_value;
														var el_vl = $(this);
														$(this).children().each(function(){
															var vl = $(this).attr('value').toUpperCase();
															if(vl == vl_dt) {
																el_vl.val($(this).attr('value'));
															}
														});
													}
												}
											}
										}
									}
								} else if($(this).prop('multiple') === true) {
									var $t = $(this);
									$.each(response[$(this).attr('id')],function(i){
										$t.find('[value="'+response[$t.attr('id')][i]+'"]').prop('selected',true);
									});
								}
							}
						});
						$('#modal-form .modal-title').html(mtitle);
						$('#modal-form').modal();
						if($('#password').length > 0) {
							$('#password').attr('data-validation','min-length:6').attr('placeholder',lang.kosongkan_jika_tidak_diubah).val('');
						}
						if ($('#modal-form form .icp').length > 0) {
							$('#modal-form form .icp').each(function(){
								$(this).closest('.input-group').find('.input-group-text').html('<i class="'+$(this).val()+'"></i>');
							});
						}
						if($('#modal-form select').length > 0 && proccess == false) {
							$('#modal-form select').trigger('change');
						}
						$('#modal-form .modal-footer').html('');
						var footer_text = '';
						var create_info = '';
						var update_info = '';
						if(typeof response['create_by'] != 'undefined' && typeof response['create_at'] != 'undefined') {
							if(response['create_at'] != null && response['create_at'] != '0000-00-00 00:00:00') {
								var create_by = response['create_by'] == '' ? 'Unknown' : response['create_by'];
								var create_at = response['create_at'].split(' ');
								var tanggal_c = create_at[0].split('-');
								var waktu_c = create_at[1].split(':');
								var date_c = tanggal_c[2]+'/'+tanggal_c[1]+'/'+tanggal_c[0]+' '+waktu_c[0]+':'+waktu_c[1];
								create_info += '<small>' + lang.dibuat_oleh + ' <strong>' + create_by + ' </strong> @ ' + date_c + '</small>';
							}
						}
						if(typeof response['update_by'] != 'undefined' && typeof response['update_at'] != 'undefined') {
							if(response['update_at'] != null && response['update_at'] != '0000-00-00 00:00:00') {
								var update_by = response['update_by'] == '' ? 'Unknown' : response['update_by'];
								var update_at = response['update_at'].split(' ');
								var tanggal_u = update_at[0].split('-');
								var waktu_u = update_at[1].split(':');
								var date_u = tanggal_u[2]+'/'+tanggal_u[1]+'/'+tanggal_u[0]+' '+waktu_u[0]+':'+waktu_u[1];
								update_info += '<small>' + lang.diperbaharui_oleh + ' <strong>' + update_by + ' </strong> @ ' + date_u + '</small>';
							}
						}
						if(create_info || update_info) {
							footer_text += '<div class="w-100">';
							footer_text += create_info;
							footer_text += update_info;
							footer_text += '</div>';
						}
						if(footer_text) {
							$('#modal-form .modal-footer').html(footer_text).removeClass('hidden');
						}
					} else {
						cAlert.open(response['message'],response['status']);
					}
					proccess = true;
					if(typeof openCallback != 'undefined' && openCallback && typeof window[openCallback] === 'function') {
						var act = window[openCallback];
						act();
					}
				}
			});
		}
	}
});
$('.btn-act-import').click(function(){
	$('#modal-import input[type=hidden]').val('');
	$('#modal-import form')[0].reset();
	$('#modal-import .fileupload-preview').html('');
	$('#modal-import .modal-footer').html('').addClass('hidden');
	$('#modal-import').modal();
});
$('select[data-child]').change(function(){
	var data_child = $(this).attr('data-child');
	var spl_child = data_child.split('|');
	if($('[name="' + spl_child[0] + '"]').length == 1 && proccess) {
		if($(this).val() != '') {
			var childUrl = '';
			if(typeof $(this).attr('data-childdata') == 'undefined') {
				var curUrl = $(this).closest('form').attr('action');
				var parseUrl = curUrl.split('/');
				var lastPath = parseUrl[parseUrl.length - 1];
				if(lastPath == '') lastPath = parseUrl[parseUrl.length - 2];
				childUrl = curUrl.replace(lastPath,'get_' + $(this).attr('data-child'));
			} else {
				childUrl = $(this).attr('data-childdata');
			}
			readonly_ajax = false;
			$.each(spl_child,function(i,d){
				$('[name="'+d+'"]').html('<option value="">' + lang.memuat_data + '...</option>');
			});
			$.ajax({
				url 		: childUrl,
				data 		: { field : $(this).attr('name'), value : $(this).val(), target : $(this).attr('data-child') },
				type 		: 'post',
				dataType 	: 'json',
				success 	: function(response) {
					$.each(response,function(k,v){
						$('[name="'+k+'"]').html(v);
						recursive_change(k);
					});
					readonly_ajax = true;
				}
			});
		} else {
			$.each(spl_child,function(i,d){
				$('[name="' + d + '"]').html('<option value=""></option>');
				recursive_change(d);
			});
		}
	}
});
$('select[data-target]').change(function(){
	var value = $(this).find('option:selected').text();
	$('[name="'+$(this).attr('data-target')+'"]').val(value);
});
$(document).on('click','.btn-delete',function(e){
	e.preventDefault();
	if(typeof $(this).attr('data-action') != 'undefined') urlDelete = $(this).attr('data-action');
	del_id = $(this).attr('data-id');
	cConfirm.open(lang.anda_yakin_menghapus_data_ini + '?','deleteData');
});
$(document).on('change','select[multiple]',function(){
	if($(this).find('option[value=all]').is(':selected')) {
		$(this).children('option').each(function(){
			if($(this).attr('value') != 'all') {
				$(this).prop('selected',false);
				$(this).parent().parent().find('.select2-selection__choice[title="'+$(this).text()+'"]').remove();
			}
		});
	}
});
$('.btn-act-delete').click(function(){
	del_id = '';
	var count = 0;
	$('table tbody input[type="checkbox"]:checked').each(function(k,v){
		if(k == 0) del_id += $(this).attr('value');
		else del_id += ',' + $(this).attr('value');
		count++;
	});
	if(count == 0) {
		cAlert.open(lang.tidak_ada_data_yang_dipilih);
	} else {
		cConfirm.open(count + ' ' + lang.data_dipilih + '. ' + lang.anda_yakin_menghapus_data_ini + '?','deleteData');
	}
});
$('.btn-act-active').click(function(){
	act_id = '';
	act_val = $(this).attr('data-value');
	var count = 0;
	$('table tbody input[type="checkbox"]:checked').each(function(k,v){
		if(k == 0) act_id += $(this).attr('value');
		else act_id += ',' + $(this).attr('value');
		count++;
	});
	if(count == 0) {
		cAlert.open(lang.tidak_ada_data_yang_dipilih);
	} else {
		cConfirm.open(count + ' ' + lang.data_dipilih + '. ' + lang.anda_yakin_mengubah_data_ini + '?','activeData');
	}
});
$('.autocomplete').each(function(){
	var $t = $(this);
	var _cache = typeof $t.attr('data-cache') != 'undefined' && $t.attr('data-cache') == 'true' ? true : false;
	var noCache = _cache ? false : true;
	$t.autocomplete({
		serviceUrl: $t.attr('data-source'),
		groupBy: 'group',
		noCache: noCache,
		showNoSuggestionNotice: true,
		noSuggestionNotice: lang.data_tidak_ditemukan,
		onSearchStart: function(query) {
			readonly_ajax = false;
			is_autocomplete = true;
			if($(this).parent().find('.autocomplete-spinner').length == 0) {
				$(this).parent().append('<i class="fa-spinner spin autocomplete-spinner"></i>');
			}
		}, onSearchComplete: function (query, suggestions) {
			is_autocomplete = false;
			$(this).parent().find('.autocomplete-spinner').remove();
		}, onSearchError: function (query, jqXHR, textStatus, errorThrown) {
			is_autocomplete = false;
			$(this).parent().find('.autocomplete-spinner').remove();
		}, onSelect: function (suggestion) {
			if(Object.prototype.toString.call(suggestion.data) === '[object Object]') {
				$('#' + $t.attr('data-target')).val(suggestion.data.data);
			} else {
				$('#' + $t.attr('data-target')).val(suggestion.data);
			}
		}
	});
});
$('.input-file').keypress(function(){
	return false;
});
$(document).on('click','.btn-act-view',function(e){
	e.preventDefault();
	var act = window['detail_callback'];
	if(typeof act == 'function') {
		act($(this).attr('data-id'));
	} else {
		var curUrl = document.location.href;
		curUrl = curUrl.replace(base_url,'');
		var scurUrl = curUrl.split('/');
		var das = scurUrl[0];
		var url_detail = base_url + 'home/detail?t=' + $('[data-serverside]').attr('data-table') + '&i=' + $(this).attr('data-id') + '&das=' + das;
		readonly_ajax = false;
		$.get(url_detail,function(res){
			cInfo.open(lang.detil,res,{modal_lg:false});
		});
	}
});
$(document).ready(function(){
	if(typeof window.CKEDITOR !== 'undefined' && $('[data-editor]').length > 0) {
		if($('.modal [data-editor]').length > 0) {
			$('[data-editor]').each(function(){
				if($(this).closest('.modal').length == 1) {
					$(this).closest('.modal').removeAttr('tabindex');
				}
			});
			setTimeout(function(){
				load_ckeditor();
			},500);
		} else {
			load_ckeditor();
		}
	}
});
function load_ckeditor() {
	$('[data-editor]').each(function(){
		var c_h 	= $(this).closest('.modal').length == 1 ? '250' : '400';
		var c_id 	= $(this).attr('id');
		if($(this).attr('data-editor') == 'inline') {
			CKEDITOR.inline( c_id ,{
				toolbar : [
					{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
					{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
					{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
					{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
					'/',
					{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
					{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
					{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
				],
				filebrowserImageBrowseUrl : base_url + 'assets/plugins/kcfinder/index.php?type=images',
				width : 'auto',
				height : c_h,
				language : $('meta[name="applang"]').attr('content')
			});
		} else {
			CKEDITOR.replace( c_id ,{
				toolbar : [
					{ name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
					{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
					{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
					'/',
					{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
					{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
					{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
					{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
					{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
					{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
					{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
				],
				filebrowserImageBrowseUrl : base_url + 'assets/plugins/kcfinder?type=images',
				width : 'auto',
				height : c_h,
				language : $('meta[name="applang"]').attr('content')
			});
		}
		CKEDITOR.config.contentsCss = base_url + 'assets/css/template-pdf.css?' + rand();
		CKEDITOR.instances[c_id].on('change', function() {
			var vdata = CKEDITOR.instances[c_id].getData();
			$('#' + c_id).val(vdata);
			$('#' + c_id).parent().find('span.error').remove();
		});
	});
}
$('.modal-body.wizard .nav-tabs li a').click(function(e){
	e.preventDefault();
});
$('.modal-body.wizard .nav-link').click(function(e){
	var target 	= $('.modal-body.wizard .nav-link.active').attr('href');
	var pane 	= target.replace('#','');

	if(typeof $(target).find('.btn-next').attr('data-trigger') != 'undefined') {
		if($(target).find('.btn-next').attr('data-trigger') != "") {
			var act = window[$(target).find('.btn-next').attr('data-trigger')];
			if(typeof act == 'function') {
				next = act();
			}
		}
	}
	validation(pane);
	if($(target).find('span.error').length > 0) {
		return false;
	}
	return true;
});
$(document).on('click','.btn-next',function(e){
	e.preventDefault();
	var pane = $(this).closest('.tab-pane').attr('id');
	var next = true;
	if(typeof $(this).attr('data-trigger') !== 'undefined') {
		if($(this).attr('data-trigger') != "") {
			var act = window[$(this).attr('data-trigger')];
			if(typeof act == 'function') {
				next = act();
			}
		}
	}
	if(validation(pane) && next) {
		$('.modal-body.wizard a').removeClass('active').attr('aria-selected','false');
		$('#' + $(this).attr('data-target') + '-tab').addClass('active').attr('aria-selected','true').attr('data-toggle','tab');
		$('.tab-content .tab-pane').removeClass('show').removeClass('active');
		$('#' + $(this).attr('data-target')).addClass('show').addClass('active');
	}
});
$(document).on('click','.btn-prev',function(e){
	e.preventDefault();
	$('.modal-body.wizard a').removeClass('active').attr('aria-selected','false');
	$('#' + $(this).attr('data-target') + '-tab').addClass('active').attr('aria-selected','true');
	$('.tab-content .tab-pane').removeClass('show').removeClass('active');
	$('#' + $(this).attr('data-target')).addClass('show').addClass('active');
});
