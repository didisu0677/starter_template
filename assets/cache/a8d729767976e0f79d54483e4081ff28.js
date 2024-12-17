
function add_row_anggota() {
	konten = '<div class="form-group row">'
			+ '<div class="offset-sm-3 col-sm-7 col-9">'
			+ '<input type="text" name="anggota[]" autocomplete="off" class="form-control anggota">'
			+ '<input type="hidden" name="id_anggota[]" class="id_anggota">'
			+ '</div>'
			+ '<div class="col-sm-2 col-3">'
			+ '<button type="button" class="btn btn-block btn-danger btn-icon-only btn-remove-anggota"><i class="fa-times"></i></button>'
			+ '</div>'
			+ '</div>';
	$('#additional-anggota').append(konten);
	cAutocomplete();
}
$('.btn-add-anggota').click(function(){
	add_row_anggota();
});
$(document).on('click','.btn-remove-anggota',function(){
	$(this).closest('.form-group').remove();
});
$(document).on('click','.btn-input',function(){
	proccess = false;
	var mtitle = '';
	$('#modal-form form')[0].reset();
	$('#additional-anggota').html('');
	if($(this).data('id') == 0) {
		mtitle = typeof $(this).attr('aria-label') != 'undefined' ? $(this).attr('aria-label') : lang.tambah;
		$('#modal-form [type="submit"]').text(lang.simpan);
	} else {
		mtitle = lang.ubah;
		$('#modal-form [type="submit"]').text(lang.perbaharui);
	}
	$('#modal-form form .is-invalid').each(function(){
		$(this).removeClass('is-invalid');
		$(this).closest('.form-group').find('.error').remove();
	});
	cAutocomplete();
	$('.id_anggota').val('');
	if($(this).attr('data-id') == '0') {
		$('#modal-form .modal-title').html(mtitle);
		$('#modal-form').modal();
		$('#modal-form [name="id"]').val(0);
		$('#modal-form .modal-footer').addClass('hidden');
		proccess = true;
	} else {
		$.ajax({
			url			: base_url + 'settings/obrolan/get_data',
			data 		: {'id':$(this).attr('data-id')},
			type		: 'post',
			cache		: false,
			dataType	: 'json',
			success		: function(response) {
				if(typeof response['status'] == 'undefined' || typeof response['message'] == 'undefined') {
					$('#id').val(response.id);
					$('#nama').val(response.nama);
					var n = response.detail.length - 3;
					for(var x=1; x<=n; x++) {
						add_row_anggota();
					}
					$('.anggota').each(function(k,v){
						if(typeof response.detail[k] != 'undefined') {
							$(this).val(response.detail[k].nama);
							$(this).parent().find('.id_anggota').val(response.detail[k].id_user);
						}
					});
					$('#aktif_mulai').val(cDate(response.aktif_mulai,true));
					$('#aktif_selesai').val(cDate(response.aktif_selesai,true));
					if(response.is_active == '0') $('#is_active').prop('checked',false);
					$('#modal-form .modal-title').html(mtitle);
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
							create_info += '<small>' + lang.dibuat_oleh + ' <strong>' + create_by + ' </strong> @ ' + date_c + '</small>';
						}
					}
					if(typeof response['update_by'] != 'undefined' && typeof response['update_at'] != 'undefined') {
						if(response['update_at'] != '0000-00-00 00:00:00') {
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
			}
		});
	}
});
$(document).on('blur','.anggota',function(){
	if($(this).parent().find('.id_anggota').val() == '0' || $(this).parent().find('.id_anggota').val() == '') {
		$(this).val('');
	}
});
function cAutocomplete() {
	$('.anggota').autocomplete({
		serviceUrl: base_url + 'settings/obrolan/get_user',
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
			$(this).parent().find('.id_anggota').val(suggestion.data);
			var n = 0;
			$('.id_anggota').each(function(){
				if($(this).val() == suggestion.data) n++;
			});
			if(n > 1) {
				$(this).parent().find('.id_anggota').val('');
				$(this).val('');
			}
		}
	});
}
$(document).on('click','.btn-export',function(){
	$('#id_export').val($(this).attr('data-id'));
	$('#modal-export').modal();
});
$('#form-export').submit(function(e){
	e.preventDefault();
	var params = {
		'id_export' 		: $('#id_export').val(),
		'periode' 			: $('#periode').val(),
		'csrf_token' 		: $('#csrf_token').val()
	};
	var url = $(this).attr('action');
	$.redirect(url, params, "POST", "_blank"); 
});
