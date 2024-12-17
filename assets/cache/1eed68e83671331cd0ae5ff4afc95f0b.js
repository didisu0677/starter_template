
$(document).on('click','.btn-input',function(){
	$('#modal-form form')[0].reset();
	$('#modal-form .modal-footer').addClass('hidden').html('');
	$('#modal-form [name="id"]').val(0);
	$('.tab-pane').removeClass('show').removeClass('active');
	$('.tab-pane').first().addClass('show').addClass('active');
	$('.nav-tabs .nav-link').removeClass('active');
	$('.nav-tabs .nav-link').first().addClass('active');
	$('.chk-all').prop('indeterminate',false).prop('checked',false);
	$('#modal-form form .is-invalid').each(function(){
		$(this).removeClass('is-invalid');
		$(this).closest('.form-group').find('.error').remove();
	});
	if($(this).data('id') == 0) {
		$('#modal-form .modal-title').html('Tambah');
		$('#modal-form [type="submit"]').text('Simpan');
		$('#modal-form').modal();
	} else {
		$('#modal-form .modal-title').html('Edit');
		$('#modal-form [type="submit"]').text('Update');
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
				if(typeof response['status'] == 'undefined' && typeof response['message'] == 'undefined') {
					$('#modal-form [name="id"]').val(response.id);
					$('#modal-form [name="nama"]').val(response.nama);
					$('#modal-form [name="keterangan"]').val(response.keterangan);
					if(response.is_active == '0') {
						$('#modal-form [name="is_active"]').prop('checked',false);
					}
					$.each(response.access,function(k,v){
						if(response.access[k].act_view == '1') $('#modal-form [name="act_view['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_input == '1') $('#modal-form [name="act_input['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_edit == '1') $('#modal-form [name="act_edit['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_delete == '1') $('#modal-form [name="act_delete['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_additional == '1') $('#modal-form [name="act_additional['+response.access[k].id_menu+']"]').prop('checked',true);
						var f = $('#modal-form [name="act_view['+response.access[k].id_menu+']"]').closest('.form-group');
						if(f.find('.chk-child:enabled:checked').length > 0) {
							if(f.find('.chk-child:enabled:checked').length == f.find('.chk-child:enabled').length) {
								f.find('.chk-all').prop('checked',true);
							} else {
								f.find('.chk-all').prop('indeterminate',true);
							}
						}
					});
					$('#modal-form').modal();
					$('#modal-form .modal-footer').html('');
					var footer_text = '';
					var create_info = '';
					var update_info = '';
					if(typeof response['create_by'] != 'undefined' && typeof response['create_at'] != 'undefined') {
						if(response['create_at'] != '0000-00-00 00:00:00') {
							var create_by = response['create_by'] == '' ? 'Unknown' : response['create_by'];
							var create_at = response['create_at'].split(' ');
							var tanggal_c = create_at[0].split('-');
							var waktu_c = create_at[1].split(':');
							var date_c = tanggal_c[2]+'/'+tanggal_c[1]+'/'+tanggal_c[0]+' '+waktu_c[0]+':'+waktu_c[1];
							create_info += '<small>Dibuat oleh <strong>' + create_by + ' </strong> @ ' + date_c + '</small>';
						}
					}
					if(typeof response['update_by'] != 'undefined' && typeof response['update_at'] != 'undefined') {
						if(response['update_at'] != '0000-00-00 00:00:00') {
							var update_by = response['update_by'] == '' ? 'Unknown' : response['update_by'];
							var update_at = response['update_at'].split(' ');
							var tanggal_u = update_at[0].split('-');
							var waktu_u = update_at[1].split(':');
							var date_u = tanggal_u[2]+'/'+tanggal_u[1]+'/'+tanggal_u[0]+' '+waktu_u[0]+':'+waktu_u[1];
							update_info += '<small>Diupdate oleh <strong>' + update_by + ' </strong> @ ' + date_u + '</small>';
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
			}
		});
	}
});
$('.chk-all').click(function(){
	if($(this).is(':checked')) {
		$(this).closest('.form-group').find('.chk-child:enabled').prop('checked',true);
		var c0 = $(this).closest('.form-group').find('.chk-child').last().attr('data-parent');
		var c1 = $('#' + c0).attr('data-parent');
		var c2 = $('#' + c1).attr('data-parent');
		var c3 = $('#' + c2).attr('data-parent');
		$('#' + c0).prop('checked',true);
		$('#' + c1).prop('checked',true);
		$('#' + c2).prop('checked',true);
		$('#' + c3).prop('checked',true);
		if($('#' + c0).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c0).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c1).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c1).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c2).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c2).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c3).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c3).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
	} else {
		$(this).closest('.form-group').find('.chk-child:enabled').prop('checked',false);
		$('[data-parent="' + $(this).closest('.form-group').find('.chk-child').first().attr('id') + '"]').each(function(){
			$(this).prop('checked',false);
			$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
			$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
				$(this).prop('checked',false);
				$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
				$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
					$(this).prop('checked',false);
					$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
						$(this).prop('checked',false);
						$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					});
				});
			});
		});
	}
});
$('.chk-child').click(function(){
	if($(this).is(':checked')) {
		var c0 = $(this).attr('data-parent');
		var c1 = $('#' + c0).attr('data-parent');
		var c2 = $('#' + c1).attr('data-parent');
		var c3 = $('#' + c2).attr('data-parent');
		$('#' + c0).prop('checked',true);
		$('#' + c1).prop('checked',true);
		$('#' + c2).prop('checked',true);
		$('#' + c3).prop('checked',true);
		if($('#' + c0).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c0).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c1).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c1).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c2).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c2).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c3).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c3).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
	} else {
		$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
			$(this).prop('checked',false);
			$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
			$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
				$(this).prop('checked',false);
				$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
				$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
					$(this).prop('checked',false);
					$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
						$(this).prop('checked',false);
						$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					});
				});
			});
		});
	}
	if($(this).closest('.form-group').find('.chk-child:enabled:checked').length > 0) {
		if($(this).closest('.form-group').find('.chk-child:enabled').length == $(this).closest('.form-group').find('.chk-child:enabled:checked').length) {
			$(this).closest('.form-group').find('.chk-all').prop('indeterminate',false).prop('checked',true);
		} else {
			$(this).closest('.form-group').find('.chk-all').prop('indeterminate',true).prop('checked',false);
		}
	} else {
		$(this).closest('.form-group').find('.chk-all').prop('indeterminate',false).prop('checked',false);
	}
});
