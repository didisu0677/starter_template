
	var del_lang = '';
	$('.btn-add-lang').click(function(e){
		e.preventDefault();
		$('#bahasa').val('');
		$('[name="flag"]').parent().find('img').attr('src',$('[name="flag"]').parent().find('img').attr('data-origin'));
		$('#modal-form').modal();
	});
	$(document).on('click','.btn-delete-lang',function(e){
		e.preventDefault();
		del_lang = $(this).attr('data-id');
		cConfirm.open(lang.anda_yakin_menghapus_data_ini+'?','deleteLang');
	});
	function deleteLang(){
		$.ajax({
			url : base_url + 'settings/bahasa/delete',
			data : {bahasa: del_lang},
			type : 'post',
			dataType : 'json',
			success : function(response) {
				if(response.status == 'success') {
					cAlert.open(response.message,response.status,'reload');
				} else {
					cAlert.open(response.message,response.status);
				}
			}
		});
	}
